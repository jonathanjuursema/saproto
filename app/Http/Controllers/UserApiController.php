<?php

namespace App\Http\Controllers;

use App\Models\AchievementOwnership;
use App\Models\Address;
use App\Models\CommitteeMembership;
use App\Models\OrderLine;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserApiController extends Controller
{
    /** @return User|null */
    public function getUser()
    {
        return Auth::user();
    }

    /** @return string|false */
    public function getUserProfilePicture()
    {
        return json_encode((object) [
            's' => Auth::user()->generateSmallPhotoPath(),
            'm' => Auth::user()->generateMediumPhotoPath(),
            'l' => Auth::user()->generateLargePhotoPath(),
        ]);
    }

    /** @return Address */
    public function getAddress()
    {
        return Auth::user()->address;
    }

    /**
     * @return Collection|CommitteeMembership[]
     */
    public function getCommittees()
    {
        return CommitteeMembership::where('user_id', Auth::id())->with('committee')->get()->where('is_society', false);
    }

    /**
     * @return Collection|CommitteeMembership[]
     */
    public function getSocieties()
    {
        return CommitteeMembership::where('user_id', Auth::id())->with('committee')->get()->where('is_society', true);
    }

    /**
     * @return Collection|AchievementOwnership[]
     */
    public function getAchievements()
    {
        return AchievementOwnership::where('user_id', Auth::id())->with('achievement')->get();
    }

    /**
     * @return Collection|OrderLine[]
     */
    public function getPurchases(Request $request)
    {
        $validated = $request->validate([
            'from' => 'date',
            'to' => 'date',
        ]);

        $orderlines = Orderline::where('user_id', Auth::id())->with('product');

        if ($request->has('from')) {
            $orderlines = $orderlines->where('created_at', '>', $validated['from']);
        }
        if ($request->has('to')) {
            $orderlines = $orderlines->where('created_at', '<', $validated['to']);
        }

        $orderlines = $orderlines->orderBy('created_at', 'DESC');
        if (! $request->has('from') && ! $request->has('to')) {
            $orderlines = $orderlines->limit(100);
        }

        return $orderlines->get();
    }

    /** @return int */
    public function getNextWithdrawal()
    {
        return $orderlines = Orderline::where('user_id', Auth::id())->whereNull('payed_with_bank_card')->whereNull('payed_with_cash')->whereNull('payed_with_mollie')->whereNull('payed_with_withdrawal')->sum('total_price');
    }

    /** @return int */
    public function getPurchasesMonth()
    {
        $orderlines = OrderLine::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y-m');
        });

        $total = 0;
        if ($orderlines->has(date('Y-m'))) {
            $selected_orders = $orderlines[Carbon::parse(date('Y-m'))->format('Y-m')];
            foreach ($selected_orders as $orderline) {
                $total += $orderline->total_price;
            }
        }

        return $total;
    }
}
