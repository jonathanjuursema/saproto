<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Event;
use App\Models\MollieTransaction;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\User;
use Auth;
use Carbon;
use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mollie;
use Redirect;
use Session;

class MollieController extends Controller
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        $user = $request->input('user_id') ? User::findOrFail($request->input('user_id')) : null;

        $transactions = MollieTransaction::query()
            ->when($user, function ($query, $user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->paginate(15);

        return view('omnomcom.mollie.list', ['user' => $user, 'transactions' => $transactions]);
    }

    /**
     * @return RedirectResponse
     */
    public function pay(Request $request)
    {
        $cap = intval($request->input('cap'));
        $total = 0;
        $requested_method = $request->input('method');
        $selected_method = null;
        $use_fees = config('omnomcom.mollie')['use_fees'];
        $available_methods = $use_fees ? self::getPaymentMethods() : null;

        $orderlines = [];
        $unpaid_orderlines = OrderLine::query()
            ->where('user_id', Auth::id())
            ->whereNull('payed_with_cash')
            ->whereNull('payed_with_bank_card')
            ->whereNull('payed_with_mollie')
            ->whereNull('payed_with_withdrawal')
            ->orderBy('total_price', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($unpaid_orderlines->min('total_price') > $cap) {
            Session::flash(
                'flash_message',
                'You cannot complete a purchase using this cap. Please try to increase the maximum amount you wish to pay!'
            );

            return Redirect::back();
        }

        foreach ($unpaid_orderlines as $orderline) {
            if ($total + $orderline->total_price <= $cap) {
                $orderlines[] = $orderline->id;
                $total += $orderline->total_price;
            } else {
                break;
            }
        }

        if ($use_fees) {
            $selected_method = $available_methods->filter(function ($method) use ($requested_method) {
                return $method->id === $requested_method;
            });

            if ($selected_method->count() === 0) {
                Session::flash('flash_message', 'The selected payment method is unavailable, please select a different method');

                return Redirect::back();
            }

            $selected_method = $selected_method->first();

            if (
                $total < floatval($selected_method->minimumAmount->value) ||
                $total > floatval($selected_method->maximumAmount->value)
            ) {
                Session::flash('flash_message', 'You are unable to pay this amount with the selected method!');

                return Redirect::back();
            }
        }

        $transaction = self::createPaymentForOrderlines($orderlines, $selected_method);

        return Redirect::away($transaction->payment_url);
    }

    /**
     * @param  int  $id
     * @return View
     *
     * @throws Exception
     */
    public function status($id)
    {
        /** @var MollieTransaction $transaction */
        $transaction = MollieTransaction::findOrFail($id);
        if ($transaction->user->id != Auth::id() && ! Auth::user()->can('board')) {
            abort(403, 'You are unauthorized to view this transaction.');
        }
        $transaction = $transaction->updateFromWebhook();

        return view('omnomcom.mollie.status', [
            'transaction' => $transaction,
            'mollie' => Mollie::api()
                ->payments()
                ->get($transaction->mollie_id),
        ]);
    }

    /**
     * @param  string  $month
     * @return View|RedirectResponse
     */
    public function monthly(Request $request, $month)
    {
        if (strtotime($month) === false) {
            Session::flash('flash_message', 'Invalid date: '.$month);

            return Redirect::back();
        }

        $month = Carbon::parse($month);
        $start = $month->copy()->startOfMonth();
        if ($start->isWeekend()) {
            $start->nextWeekday();
        }
        $end = $month->copy()->addMonth()->startOfMonth();
        if ($end->isWeekend()) {
            $end->nextWeekday();
        }

        // We do one massive query to reduce the number of queries.
        $orderlines = DB::table('orderlines')
            ->join('products', 'orderlines.product_id', '=', 'products.id')
            ->join('accounts', 'products.account_id', '=', 'accounts.id')
            ->select('orderlines.*', 'accounts.account_number', 'accounts.name')
            ->whereNotNull('orderlines.payed_with_mollie')
            ->whereBetween('orderlines.created_at', [$start, $end])
            ->get();

        return view('omnomcom.accounts.orderlines-breakdown', [
            'accounts' => Account::generateAccountOverviewFromOrderlines($orderlines),
            'title' => 'Account breakdown for Mollie transactions between '.$start->format('d-m-Y').' and '.$end->format('d-m-Y'),
        ]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function receive($id)
    {
        $transaction = MollieTransaction::findOrFail($id);

        $flash_message = 'Unknown error';
        if ($transaction->user_id == Auth::id()) {
            switch (MollieTransaction::translateStatus($transaction->status)) {
                case 'failed':
                    $flash_message = 'Your payment has failed';
                    break;
                case 'open':
                    $flash_message = 'Your payment is still open';
                    break;
                case 'paid':
                    $flash_message = 'Your payment was completed successfully!';
                    break;
            }
            Session::flash('flash_message', $flash_message);
        }

        if (Session::has('mollie_paid_tickets')) {
            $event_id = Session::get('mollie_paid_tickets');
            Session::remove('mollie_paid_tickets');
            $isMember = Auth::user()->is_member;

            switch (MollieTransaction::translateStatus($transaction->status)) {
                case 'failed':
                    if ($isMember) {
                        $flash_message = 'Your payment has failed, the tickets are still yours but they are now listed as a withdrawal.';
                    } else {
                        $flash_message = 'Your payment has failed, the tickets have not been added to your account, please retry the purchase.';
                    }
                    break;
                case 'open':
                    $flash_message = 'Your payment is still open, the payment can still be completed.';
                    break;
                case 'paid':
                    $flash_message = 'Your payment was completed successfully! The tickets have been mailed to you!';
                    break;
            }
            Session::flash('flash_message', $flash_message);

            return Redirect::route('event::show', ['id' => Event::findOrFail($event_id)->getPublicId()]);
        }

        return Redirect::route('omnomcom::orders::list');
    }

    /**
     * @param  int  $id
     *
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
     * @param  int[]  $orderlines
     * @return MollieTransaction
     */
    public static function createPaymentForOrderlines($orderlines, $selected_method)
    {
        $total = OrderLine::whereIn('id', $orderlines)->sum('total_price');

        if (config('omnomcom.mollie')['use_fees']) {
            $fee = round(
                $selected_method->pricing[0]->fixed->value +
                    $total * (floatval($selected_method->pricing[0]->variable) / 100),
                2
            );
            if ($fee > 0) {
                $orderline = OrderLine::findOrFail(
                    Product::findOrFail(config('omnomcom.mollie')['fee_id'])->buyForUser(
                        Auth::user(),
                        1,
                        $fee,
                        null,
                        null,
                        null,
                        'mollie_transaction_fee'
                    )
                );
                $orderline->save();
                $orderlines[] = $orderline->id;
                $total += $fee;
            }
        }

        $transaction = MollieTransaction::create([
            'user_id' => Auth::id(),
            'mollie_id' => 'temp',
            'status' => 'draft',
        ]);

        $total = number_format($total, 2, '.', '');
        $properties = [
            'amount' => [
                'currency' => 'EUR',
                'value' => $total,
            ],
            'method' => config('omnomcom.mollie')['use_fees'] ? $selected_method->id : null,
            'description' => 'OmNomCom Settlement (€'.$total.')',
            'redirectUrl' => route('omnomcom::mollie::receive', ['id' => $transaction->id]),
        ];

        if (config('omnomcom.mollie')['has_webhook']) {
            $properties['webhookUrl'] = route('webhook::mollie', ['id' => $transaction->id]);
        }

        $mollie = Mollie::api()
            ->payments()
            ->create($properties);

        $transaction->mollie_id = $mollie->id;
        $transaction->amount = $mollie->amount->value;
        $transaction->payment_url = $mollie->getCheckoutUrl();
        $transaction->save();

        OrderLine::whereIn('id', $orderlines)->update(['payed_with_mollie' => $transaction->id]);

        return $transaction;
    }

    /**
     * @param  string  $month
     * @return int
     */
    public static function getTotalForMonth($month)
    {
        $month = Carbon::parse($month);
        $start = $month->copy()->startOfMonth();
        if ($start->isWeekend()) {
            $start->nextWeekday();
        }
        $end = $month->copy()->addMonth()->startOfMonth();
        if ($end->isWeekend()) {
            $end->nextWeekday();
        }

        return OrderLine::whereNotNull('payed_with_mollie')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_price');
    }

    /**
     * @return null|object
     */
    public static function getPaymentMethods()
    {
        if (app()->environment('local')) {
            return null;
        }

        $api_response = Mollie::api()
            ->methods()
            ->all([
                'locale' => 'nl_NL',
                'billingCountry' => 'NL',
                'include' => 'pricing',
            ]);
        $methodsList = (array) $api_response;

        foreach ($api_response as $index => $method) {
            if ($method->status != 'activated' || $method->resource != 'method') {
                unset($methodsList[$index]);
            }
            if (in_array($method->id, config('omnomcom.mollie')['free_methods'])) {
                $methodsList[$index]->pricing = null;
                $methodsList[$index]->pricing[0] = (object) [
                    'description' => $method->description,
                    'fixed' => (object) [
                        'value' => '0.00',
                        'currency' => 'EUR',
                    ],
                    'variable' => '0',
                ];
            }
        }

        return collect($methodsList);
    }
}
