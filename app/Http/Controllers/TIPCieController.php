<?php

namespace App\Http\Controllers;

use Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Account;
use App\Models\OrderLine;

class TIPCieController extends Controller
{
    /**
     * @return View
     */
    public function orderIndex(Request $request)
    {
        $date = $request->has('date') ? $request->date : null;

        $tipcieProducts = Account::find(config('omnomcom.tipcie-account'))->products;
        $tipcieProductIds = $tipcieProducts->pluck('id')->toArray();
        $tipcieProductNames = $tipcieProducts->pluck('name')->toArray();

        $tipcieOrders = [];

        $dailyAmount = 0;
        $dailyTotal = 0;

        $pinOrders = [];
        $pinTotal = 0;

        $orders = OrderLine::where('created_at', '>=', ($date ? Carbon::parse($date)->addHours(6)->format('Y-m-d H:i:s') : Carbon::today()->format('Y-m-d H:i:s')))
            ->where('created_at', '<', ($date ? Carbon::parse($date)->addHours(30)->format('Y-m-d H:i:s') : Carbon::today()->format('Y-m-d H:i:s')))
            ->get();

        foreach ($orders as $order) {
            if (in_array($order->product_id, $tipcieProductIds)) {
                $pid = (string) $order->product_id;

                if (! array_key_exists($pid, $tipcieOrders)) {
                    $productInfo = new \stdClass();
                    $productInfo->name = $tipcieProductNames[array_search($pid, $tipcieProductIds)];
                    $productInfo->amount = 0;
                    $productInfo->totalPrice = 0;
                    $tipcieOrders[$pid] = $productInfo;
                }

                $tipcieOrders[$pid]->amount += $order->units;
                $tipcieOrders[$pid]->totalPrice += $order->total_price;
                $dailyAmount += $order->units;
                $dailyTotal += $order->total_price;

                if ($order->payed_with_bank_card) {
                    $time = (string) $order->created_at;
                    if (! array_key_exists($time, $pinOrders)) {
                        $pinOrders[$time] = 0;
                    }
                    $pinOrders[$time] += $order->total_price;
                    $pinTotal += $order->total_price;
                }
            }
        }

        return view('omnomcom.tipcie.orderhistory', ['orders' => $tipcieOrders, 'date' => $date, 'dailyTotal' => $dailyTotal, 'dailyAmount' => $dailyAmount, 'pinOrders' => $pinOrders, 'pinTotal' => $pinTotal]);
    }
}
