<?php

namespace Proto\Http\Controllers;

use AbcAeffchen\SepaUtilities\SepaUtilities;
use AbcAeffchen\Sephpa\SephpaDirectDebit;
use AbcAeffchen\Sephpa\SephpaInputException;
use Auth;
use Carbon;
use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Mail;
use Proto\Mail\OmnomcomFailedWithdrawalNotification;
use Proto\Mail\OmnomcomWithdrawalNotification;
use Proto\Models\Account;
use Proto\Models\FailedWithdrawal;
use Proto\Models\OrderLine;
use Proto\Models\Product;
use Proto\Models\User;
use Proto\Models\Withdrawal;
use Redirect;
use Response;

class WithdrawalController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        return view('omnomcom.withdrawals.index', ['withdrawals' => Withdrawal::orderBy('id', 'desc')->paginate(6)]);
    }

    /** @return View */
    public function create()
    {
        return view('omnomcom.withdrawals.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $max = ($request->has('max') ? $request->input('max') : null);
        if ($max <= 0) {
            $max = null;
        }

        $date = strtotime($request->input('date'));
        if ($date === false) {
            $request->session()->flash('flash_message', 'Invalid date.');

            return Redirect::back();
        }

        $withdrawal = Withdrawal::create([
            'date' => date('Y-m-d', $date),
        ]);

        $totalPerUser = [];
        foreach (OrderLine::whereNull('payed_with_withdrawal')->get() as $orderline) {
            if ($orderline->isPayed()) {
                continue;
            }
            if ($orderline->user === null) {
                continue;
            }
            if ($orderline->user->bank == null) {
                continue;
            }

            if (! array_key_exists($orderline->user->id, $totalPerUser)) {
                $totalPerUser[$orderline->user->id] = 0;
            }

            if ($max != null) {
                if ($totalPerUser[$orderline->user->id] + $orderline->total_price > $max) {
                    continue;
                }
            }

            $orderline->withdrawal()->associate($withdrawal);
            $orderline->save();

            $totalPerUser[$orderline->user->id] += $orderline->total_price;
        }

        foreach ($totalPerUser as $user_id => $total) {
            if ($total < 0) {
                /** @var User $user */
                $user = User::findOrFail($user_id);
                foreach ($withdrawal->orderlinesForUser($user) as $orderline) {
                    $orderline->withdrawal()->dissociate();
                    $orderline->save();
                }
            }
        }

        return Redirect::route('omnomcom::withdrawal::show', ['id' => $withdrawal->id]);
    }

    /**
     * @param int $id
     * @return View
     */
    public function show($id)
    {
        return view('omnomcom.withdrawals.show', ['withdrawal' => Withdrawal::findOrFail($id)]);
    }

    /**
     * @param $id
     * @return View
     */
    public function showAccounts($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        // We do one massive query to reduce the number of queries.
        $orderlines = DB::table('orderlines')
            ->join('products', 'orderlines.product_id', '=', 'products.id')
            ->join('accounts', 'products.account_id', '=', 'accounts.id')
            ->select('orderlines.*', 'accounts.account_number', 'accounts.name')
            ->where('orderlines.payed_with_withdrawal', $withdrawal->id)
            ->get();

        $accounts = [];

        foreach ($orderlines as $orderline) {
            // We sort by date, where a date goes from 6am - 6am.
            $sortDate = Carbon::parse($orderline->created_at)->subHours(6)->toDateString();

            // Shorthand variable names.
            $nr = $orderline->account_number;

            // Add account to dataset if not existing yet.
            if (! isset($accounts[$nr])) {
                $accounts[$nr] = (object) [
                    'byDate' => [],
                    'name' => $orderline->name,
                    'total' => 0,
                ];
            }

            // Add orderline to total account price.
            $accounts[$nr]->total += $orderline->total_price;

            // Add date to account data if not existing yet.
            if (! isset($accounts[$nr]->byDate[$sortDate])) {
                $accounts[$nr]->byDate[$sortDate] = 0;
            }

            // Add orderline to account-on-date total.
            $accounts[$nr]->byDate[$sortDate] += $orderline->total_price;
        }

        ksort($accounts);

        return view('omnomcom.accounts.orderlines-breakdown', [
            'accounts' => Account::generateAccountOverviewFromOrderlines($orderlines),
            'title' => 'Accounts of withdrawal of '.date('d-m-Y', strtotime($withdrawal->date)),
            'total' => $withdrawal->total(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->closed) {
            $request->session()->flash('flash_message', 'This withdrawal is already closed and cannot be edited.');

            return Redirect::back();
        }

        $date = strtotime($request->input('date'));
        if ($date === false) {
            $request->session()->flash('flash_message', 'Invalid date.');

            return Redirect::back();
        }

        $withdrawal->date = date('Y-m-d', $date);
        $withdrawal->save();

        $request->session()->flash('flash_message', 'Withdrawal updated.');
        return Redirect::route('omnomcom::withdrawal::show', ['id' => $withdrawal->id]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->closed) {
            $request->session()->flash('flash_message', 'This withdrawal is already closed and cannot be deleted.');

            return Redirect::back();
        }

        foreach ($withdrawal->orderlines as $orderline) {
            $orderline->withdrawal()->dissociate();
            $orderline->save();
        }

        foreach (FailedWithdrawal::where('withdrawal_id', $withdrawal->id)->get() as $failed_withdrawal) {
            $failed_withdrawal->correction_orderline->delete();
            $failed_withdrawal->delete();
        }

        $withdrawal->delete();

        $request->session()->flash('flash_message', 'Withdrawal deleted.');
        return Redirect::route('omnomcom::withdrawal::list');
    }

    /**
     * @param Request $request
     * @param $id
     * @param $user_id
     * @return RedirectResponse
     */
    public static function deleteFrom(Request $request, $id, $user_id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->closed) {
            $request->session()->flash('flash_message', 'This withdrawal is already closed and cannot be edited.');

            return Redirect::back();
        }

        /** @var User $user */
        $user = User::findOrFail($user_id);

        foreach ($withdrawal->orderlinesForUser($user) as $orderline) {
            $orderline->withdrawal()->dissociate();
            $orderline->save();
        }

        $request->session()->flash('flash_message', 'Orderlines for '.$user->name.' removed from this withdrawal.');
        return Redirect::back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $user_id
     * @return RedirectResponse
     */
    public static function markFailed(Request $request, $id, $user_id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->closed) {
            $request->session()->flash('flash_message', 'This withdrawal is already closed and cannot be edited.');
            return Redirect::back();
        }

        /** @var User $user */
        $user = User::findOrFail($user_id);

        if (FailedWithdrawal::where('user_id', $user_id)->where('withdrawal_id', $id)->first() !== null) {
            $request->session()->flash('flash_message', 'This withdrawal has already been marked as failed.');

            return Redirect::back();
        }

        /** @var Product $product */
        $product = Product::findOrFail(config('omnomcom.failed-withdrawal'));
        $total = $withdrawal->totalForUser($user);

        /** @var OrderLine $failedOrderline */
        $failedOrderline = OrderLine::findOrFail(
            $product->buyForUser(
                $user,
                1,
                $total,
                null,
                null,
                sprintf('Overdue payment due to the failed withdrawal from %s.', date('d-m-Y', strtotime($withdrawal->date))),
                sprintf('failed_withdrawal_by_%u', Auth::user()->id)
            )
        );

        FailedWithdrawal::create([
            'user_id' => $user->id,
            'withdrawal_id' => $withdrawal->id,
            'correction_orderline_id' => $failedOrderline->id,
        ])->save();

        Mail::to($user)->queue((new OmnomcomFailedWithdrawalNotification($user, $withdrawal))->onQueue('medium'));

        $request->session()->flash('flash_message', 'Withdrawal for '.$user->name.' marked as failed. User e-mailed.');
        return Redirect::back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws SephpaInputException
     */
    public static function export(Request $request, $id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        $direct_debit = new SephpaDirectDebit('Study Association Proto', $withdrawal->withdrawalId(), SephpaDirectDebit::SEPA_PAIN_008_001_02, [
            'pmtInfId' => sprintf('%s-1', $withdrawal->withdrawalId()),
            'lclInstrm' => SepaUtilities::LOCAL_INSTRUMENT_CORE_DIRECT_DEBIT,
            'seqTp' => SepaUtilities::SEQUENCE_TYPE_FIRST,
            'cdtr' => 'Study Association Proto',
            'iban' => config('proto.sepa_info')->iban,
            'bic' => config('proto.sepa_info')->bic,
            'ci' => config('proto.sepa_info')->creditor_id,
            'ccy' => 'EUR',
            'ultmtCdtr' => 'S.A. Proto',
            'reqdColltnDt' => $withdrawal->date,
        ]);

        $i = 1;
        foreach ($withdrawal->users() as $user) {
            try {
                $direct_debit->addPayment([
                    'pmtId' => sprintf('%s-1-%s', $withdrawal->withdrawalId(), $i),
                    'instdAmt' => number_format($withdrawal->totalForUser($user), 2, '.', ''),
                    'mndtId' => $user->bank->machtigingid,
                    'dtOfSgntr' => date('Y-m-d', strtotime($user->bank->created_at)),
                    'bic' => $user->bank->bic,
                    'dbtr' => $user->name,
                    'iban' => $user->bank->iban,
                ]);
                $i++;
            } catch (SephpaInputException $e) {
                abort(500, sprintf('Error for user #%s: %s', $user->id, $e->getMessage()));
            }
        }

        $response = $direct_debit->generateOutput([], false)[0];

        $headers = [
            'Content-Encoding' => 'UTF-8',
            'Content-Type' => 'text/xml; charset=UTF-8',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $response['name']),
        ];

        return Response::make($response['data'], 200, $headers);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public static function close(Request $request, $id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->closed) {
            $request->session()->flash('flash_message', 'This withdrawal is already closed and cannot be edited.');

            return Redirect::back();
        }

        foreach ($withdrawal->users() as $user) {
            $user->bank->is_first = false;
            $user->bank->save();
        }

        $withdrawal->closed = true;
        $withdrawal->save();

        $request->session()->flash('flash_message', 'The withdrawal is now closed. Changes cannot be made anymore.');
        return Redirect::back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function showForUser(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        return view('omnomcom.withdrawals.userhistory', ['withdrawal' => $withdrawal, 'orderlines' => $withdrawal->orderlinesForUser(Auth::user())]);
    }

    /**
     * Send an e-mail to all users in the withdrawal to notice them.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function email(Request $request, $id)
    {
        /** @var Withdrawal $withdrawal */
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->closed) {
            $request->session()->flash('flash_message', 'This withdrawal is already closed so e-mails cannot be sent.');

            return Redirect::back();
        }

        foreach ($withdrawal->users() as $user) {
            Mail::to($user)->queue((new OmnomcomWithdrawalNotification($user, $withdrawal))->onQueue('medium'));
        }

        $request->session()->flash('flash_message', 'All e-mails have been queued.');

        return Redirect::back();
    }

    /** @return View */
    public function unwithdrawable()
    {
        $users = [];

        foreach (OrderLine::whereNull('payed_with_withdrawal')->get() as $orderline) {
            if ($orderline->isPayed()) {
                continue;
            }
            if ($orderline->user === null) {
                Session::flash('flash_message', 'There are unpaid anonymous orderlines. Please contact the IT committee.');
                continue;
            }
            if ($orderline->user->bank) {
                continue;
            }
            if (! in_array($orderline->user->id, array_keys($users))) {
                $users[$orderline->user->id] = (object) [
                    'user' => $orderline->user,
                    'orderlines' => [],
                    'total' => 0,
                ];
            }
            $users[$orderline->user->id]->orderlines[] = $orderline;
            $users[$orderline->user->id]->total += $orderline->total_price;
        }

        return view('omnomcom.unwithdrawable', ['users' => $users]);
    }

    /** @return float|int */
    public static function openOrderlinesSum()
    {
        $sum = 0;
        foreach (OrderLine::whereNull('payed_with_withdrawal')->get() as $orderline) {
            if ($orderline->isPayed()) {
                continue;
            }
            $sum += $orderline->total_price;
        }

        return $sum;
    }

    /** @return int */
    public static function openOrderlinesTotal()
    {
        $total = 0;
        foreach (OrderLine::whereNull('payed_with_withdrawal')->get() as $orderline) {
            if ($orderline->isPayed()) {
                continue;
            }
            $total++;
        }

        return $total;
    }
}
