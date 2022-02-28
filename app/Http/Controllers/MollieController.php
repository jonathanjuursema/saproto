<?php

namespace Proto\Http\Controllers;

use Auth;
use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mollie;
use Proto\Models\Account;
use Proto\Models\Event;
use Proto\Models\MollieTransaction;
use Proto\Models\OrderLine;
use Proto\Models\Product;
use Proto\Models\User;
use Redirect;
use Session;

class MollieController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function pay(Request $request)
    {
        $cap = floatval($request->cap);
        $total = 0;

        $orderlines = [];

        foreach (OrderLine::where('user_id', Auth::id())->whereNull('payed_with_cash')->whereNull('payed_with_bank_card')->whereNull('payed_with_mollie')->whereNull('payed_with_withdrawal')->orderBy('created_at', 'asc')->get() as $orderline) {
            if ($total + $orderline->total_price > $cap) {
                break;
            }
            $orderlines[] = $orderline->id;
            $total += $orderline->total_price;
        }

        if ($total <= 0) {
            Session::flash('flash_message', 'You cannot complete a purchase using this cap. Please try to increase the maximum amount you wish to pay!');
            return Redirect::back();
        }

        $fee = config('omnomcom.mollie')['fixed_fee'] + $total * config('omnomcom.mollie')['variable_fee'];

        $orderline = OrderLine::findOrFail(Product::findOrFail(config('omnomcom.mollie')['fee_id'])->buyForUser(Auth::user(), 1, $fee, null, null, null, 'mollie_transaction_fee'));
        $orderline->save();

        $orderlines[] = $orderline->id;

        $transaction = self::createPaymentForOrderlines($orderlines);

        OrderLine::whereIn('id', $orderlines)->update(['payed_with_mollie' => $transaction->id]);

        return Redirect::to($transaction->payment_url);
    }

    /**
     * @param $id
     * @return View
     * @throws Exception
     */
    public function status($id)
    {
        /** @var MollieTransaction $transaction */
        $transaction = MollieTransaction::findOrFail($id);
        if ($transaction->user->id != Auth::id() && ! Auth::user()->can('board')) {
            abort(403, 'You are unauthorized to view this transcation.');
        }
        $transaction = $transaction->updateFromWebhook();

        return view('omnomcom.mollie.status', ['transaction' => $transaction, 'mollie' => Mollie::api()->payments()->get($transaction->mollie_id)]);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            $user = User::findOrFail($request->get('user_id'));
            return view('omnomcom.mollie.list', ['user' => $user, 'transactions' => MollieTransaction::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(15)]);
        } else {
            return view('omnomcom.mollie.list', ['user' => null, 'transactions' => MollieTransaction::orderBy('created_at', 'desc')->paginate(15)]);
        }
    }

    /**
     * @param Request $request
     * @param string $month
     * @return View|RedirectResponse
     */
    public function monthly(Request $request, $month)
    {
        if (strtotime($month) === false) {
            $request->session()->flash('flash_message', 'Invalid date: '.$month);

            return Redirect::back();
        }

        // We do one massive query to reduce the number of queries.
        $orderlines = DB::table('orderlines')
            ->join('products', 'orderlines.product_id', '=', 'products.id')
            ->join('accounts', 'products.account_id', '=', 'accounts.id')
            ->select('orderlines.*', 'accounts.account_number', 'accounts.name')
            ->whereNotNull('orderlines.payed_with_mollie')
            ->where('orderlines.created_at', 'like', $month.'-%')
            ->get();

        return view('omnomcom.accounts.orderlines-breakdown', [
            'accounts' => Account::generateAccountOverviewFromOrderlines($orderlines),
            'title' => 'Account breakdown for Mollie transactions in '.date('F Y', strtotime($month)),
        ]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function receive($id)
    {
        $transaction = MollieTransaction::findOrFail($id);
        $transaction = $transaction->updateFromWebhook();

        $completed = true;

        if ($transaction->user->id == Auth::id()) {
            if (MollieTransaction::translateStatus($transaction->status) == 'failed') {
                Session::flash('flash_message', 'Your payment was cancelled.');
                $completed = false;
            } elseif (MollieTransaction::translateStatus($transaction->status) == 'paid') {
                Session::flash('flash_message', 'Your payment was completed successfully!');
            }
        }

        if (Session::has('prepaid_tickets')) {
            $event_id = Session::get('prepaid_tickets');
            Session::remove('prepaid_tickets');
            if ($completed) {
                Session::flash('flash_message', 'Order completed succesfully! You can find your tickets on this event page.');
            } else {
                Session::flash('flash_message', 'Order failed. Pre-paid tickets where not bought. Please try your purchase again.');
            }

            return Redirect::route('event::show', ['id' => Event::findOrFail($event_id)->getPublicId()]);
        }

        return Redirect::route('omnomcom::orders::list');
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function webhook($id)
    {
        /** @var MollieTransaction $transaction */
        $transaction = MollieTransaction::findOrFail($id);
        $transaction->updateFromWebhook();
        abort(200, 'Mollie webhook processed correctly!');
    }

    /**
     * @param $orderlines
     * @return MollieTransaction
     */
    public static function createPaymentForOrderlines($orderlines)
    {
        $transaction = MollieTransaction::create([
            'user_id' => Auth::id(),
            'mollie_id' => 'temp',
            'status' => 'draft',
        ]);

        $total = OrderLine::whereIn('id', $orderlines)->sum('total_price');

        $mollie = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => strval($total),
            ],
            'description' => 'OmNomCom Settlement (€'.number_format($total, 2).')',
            'redirectUrl' => route('omnomcom::mollie::receive', ['id' => $transaction->id]),
            'webhookUrl' => route('webhook::mollie', ['id' => $transaction->id]),
        ]);

        $transaction->mollie_id = $mollie->id;
        $transaction->amount = $mollie->amount->value;
        $transaction->payment_url = $mollie->getCheckoutUrl();
        $transaction->save();

        return $transaction;
    }

    /**
     * @param string $month
     * @return int
     */
    public static function getTotalForMonth($month)
    {
        return OrderLine::whereNotNull('payed_with_mollie')
            ->where('created_at', 'LIKE', sprintf('%s-%%', $month))
            ->sum('total_price');
    }
}
