<?php

namespace App\Http\Controllers;

use Auth;
use Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Activity;
use App\Models\FailedWithdrawal;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\TicketPurchase;
use App\Models\User;
use Redirect;
use Session;

class OrderLineController extends Controller
{
    /**
     * @param  string  $date
     * @return View
     */
    public function index($date = null)
    {
        $user = Auth::user();

        $next_withdrawal = OrderLine::query()
            ->where('user_id', $user->id)
            ->whereNull('payed_with_cash')
            ->whereNull('payed_with_bank_card')
            ->whereNull('payed_with_mollie')
            ->whereNull('payed_with_withdrawal')
            ->sum('total_price');

        $orderlines = OrderLine::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->with('product')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            });

        $selected_month = $date ?? date('Y-m');

        $available_months = [];
        foreach ($orderlines->keys() as $month) {
            $month = Carbon::parse($month);
            $available_months[$month->year][] = $month->month;
        }

        $total = 0;
        if ($orderlines->has($selected_month)) {
            $selected_orders = $orderlines[Carbon::parse($date)->format('Y-m')];
            foreach ($selected_orders as $orderline) {
                if ($orderline->total_price > 0) {
                    $total += $orderline->total_price;
                }
            }
        }

        $outstanding = Activity::whereHas('users', function (Builder $query) {
            $query->where('user_id', Auth::user()->id);
        })->where('closed', false);

        $outstandingAmount = $outstanding->sum('price');
        $outstanding = $outstanding->select('event_id', 'price')->with('Event', function ($q) {
            $q->select('id', 'title');
        })->where('price', '>', 0)->get();

        $payment_methods = MollieController::getPaymentMethods();

        return view('omnomcom.orders.myhistory', [
            'user' => $user,
            'available_months' => $available_months,
            'selected_month' => $selected_month,
            'orderlines' => $orderlines->has($selected_month) ? $orderlines[$selected_month] : [],
            'next_withdrawal' => $next_withdrawal,
            'total' => $total,
            'methods' => $payment_methods ?? [],
            'use_fees' => config('omnomcom.mollie')['use_fees'],
            'outstandingAmount' => $outstandingAmount,
            'outstanding' => $outstanding,
        ]);
    }

    /**
     * @return View
     */
    public function adminindex(Request $request)
    {
        if (Auth::user()->can('alfred') && ! Auth::user()->hasRole('sysadmin')) {
            $orderlines = OrderLine::whereHas('product', function ($query) {
                $query->where('account_id', '=', config('omnomcom.alfred-account'));
            })->whereDate('created_at', (Carbon::today()));
        } else {
            $orderlines = OrderLine::whereDate('created_at', Carbon::today());
        }
        $orderlines = $orderlines->orderBy('created_at', 'desc')->paginate(20);

        return view('omnomcom.orders.adminhistory', [
            'date' => Carbon::today()->format('d-m-Y'),
            'orderlines' => $orderlines,
            'user' => null,
        ]);
    }

    /**
     * @return View
     */
    public function filterByDate(Request $request)
    {
        $date = Carbon::parse($request->input('date'))->format('d-m-Y');

        if (Auth::user()->can('alfred') && ! Auth::user()->hasRole('sysadmin')) {
            $orderlines = OrderLine::whereHas('product', function ($query) {
                $query->where('account_id', '=', config('omnomcom.alfred-account'));
            })->whereDate('created_at', Carbon::parse($date));
        } else {
            $orderlines = OrderLine::whereDate('created_at', Carbon::parse($date));
        }

        $orderlines = $orderlines->orderBy('created_at', 'desc')->paginate(20)->appends(['date' => $date]);

        return view('omnomcom.orders.adminhistory', [
            'date' => $date,
            'orderlines' => $orderlines,
            'user' => null,
        ]);
    }

    /**
     * @return View|RedirectResponse
     */
    public function filterByUser(Request $request)
    {
        $user = $request->input('user');

        if (Auth::user()->can('alfred') && ! Auth::user()->hasRole('sysadmin')) {
            $orderlines = OrderLine::whereHas('product', function ($query) {
                $query->where('account_id', '=', config('omnomcom.alfred-account'));
            })->where('user_id', $user);
        } else {
            $orderlines = OrderLine::where('user_id', $user);
        }

        $orderlines = $orderlines->orderBy('created_at', 'desc')->paginate(20)->appends(['user' => $user]);

        return view('omnomcom.orders.adminhistory', [
            'date' => null,
            'user' => User::findOrFail($user)->name,
            'orderlines' => $orderlines,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function bulkStore(Request $request)
    {
        for ($i = 0; $i < count($request->input('user')); $i++) {
            /** @var Product $product */
            $product = Product::findOrFail($request->input('product')[$i]);
            $user = User::findOrFail($request->input('user')[$i]);
            $price = ($request->input('price')[$i] != '' ? floatval(str_replace(',', '.', $request->input('price')[$i])) : $product->price);
            $units = $request->input('units')[$i];
            $product->buyForUser($user, $units, $price * $units, null, null, $request->input('description'), sprintf('bulk_add_by_%u', Auth::user()->id));
        }

        Session::flash('flash_message', 'Your manual orders have been added.');

        return Redirect::back();
    }

    /**
     * Store (a) simple orderline(s).
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        for ($u = 0; $u < count($request->input('user')); $u++) {
            for ($p = 0; $p < count($request->input('product')); $p++) {
                $user = User::findOrFail($request->input('user')[$u]);
                $product = Product::findOrFail($request->input('product')[$p]);

                $product->buyForUser($user, 1, null, null, null, null, sprintf('simple_add_by_%u', Auth::user()->id));
            }
        }

        Session::flash('flash_message', 'Your manual orders have been added.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var OrderLine $order */
        $order = OrderLine::findOrFail($id);

        if (! $order->canBeDeleted()) {
            Session::flash('flash_message', 'The orderline cannot be deleted.');

            return Redirect::back();
        }

        $order->product->stock += $order->units;
        $order->product->save();

        TicketPurchase::where('orderline_id', $id)->delete();
        FailedWithdrawal::where('correction_orderline_id', $id)->delete();

        $order->delete();

        Session::flash('flash_message', 'The orderline was deleted.');

        return Redirect::back();
    }

    /**
     * Display aggregated payment totals for cash and card payments to check the financial administration.
     *
     * @return View
     */
    public function showPaymentStatistics(Request $request)
    {
        if ($request->has('start') && $request->has('end')) {
            $start = date('Y-m-d H:i:s', strtotime($request->start));
            $end = date('Y-m-d H:i:s', strtotime($request->end));
            $total_cash = DB::table('orderlines')
                ->where('created_at', '>', $start)
                ->where('created_at', '<', $end)
                ->whereNotNull('payed_with_cash')
                ->select(DB::raw('SUM(total_price) as total'))
                ->get()[0]->total;
            $total_card = DB::table('orderlines')
                ->where('created_at', '>', $start)
                ->where('created_at', '<', $end)
                ->whereNotNull('payed_with_bank_card')
                ->select(DB::raw('SUM(total_price) as total'))
                ->get()[0]->total;

            return view('omnomcom.statistics.payments', [
                'start' => $request->start,
                'end' => $request->end,
                'total_cash' => $total_cash,
                'total_card' => $total_card,
            ]);
        } else {
            return view('omnomcom.statistics.date-select', ['select_text' => 'Select a time range over which to calculate payment totals.']);
        }
    }
}
