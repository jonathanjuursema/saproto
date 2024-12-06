<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TIPCieController extends Controller
{
    public function orderIndex(Request $request): View
    {
        $date = $request->has('date') ? $request->date : null;

        $startDate = ($date ? Carbon::parse($date)->addHours(6)->format('Y-m-d H:i:s') : Carbon::today()->format('Y-m-d H:i:s'));
        $endDate = ($date ? Carbon::parse($date)->addHours(30)->format('Y-m-d H:i:s') : Carbon::today()->format('Y-m-d H:i:s'));

        /** @var Account $tipcieAccount */
        $tipcieAccount = Account::query()->findOrFail(Config::integer('omnomcom.tipcie-account'));

        $products = Product::query()
            ->where('account_id', $tipcieAccount->id)
            ->whereHas('orderlines', function ($q) use ($startDate, $endDate) {
                $q->where('created_at', '>=', $startDate)->where('created_at', '<', $endDate);
            })->withSum(['orderlines' => function ($q) use ($startDate, $endDate) {
                $q->where('created_at', '>=', $startDate)->where('created_at', '<', $endDate);
            }], 'total_price')->withSum(['orderlines' => function ($q) use ($startDate, $endDate) {
                $q->where('created_at', '>=', $startDate)->where('created_at', '<', $endDate);
            }], 'units')->get();

        $pinOrders = DB::table('orderlines')
            ->where('orderlines.created_at', '>=', $startDate)
            ->where('orderlines.created_at', '<', $endDate)
            ->join('products', 'orderlines.product_id', '=', 'products.id')
            ->where('products.account_id', '=', $tipcieAccount->id)
            ->whereNotNull('payed_with_bank_card')->groupBy('orderlines.created_at')
            ->selectRaw('orderlines.created_at, SUM(orderlines.total_price) as total_price')->get();

        $totalPrice = $products->sum('orderlines_sum_total_price');
        $totalAmount = $products->sum('orderlines_sum_units');
        $pinTotal = $pinOrders->sum('total_price');

        return view('omnomcom.tipcie.orderhistory', ['products' => $products, 'date' => $date, 'pinOrders' => $pinOrders, 'totalPrice' => $totalPrice, 'totalAmount' => $totalAmount, 'pinTotal' => $pinTotal]);
    }
}
