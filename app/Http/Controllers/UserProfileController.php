<?php

namespace App\Http\Controllers;

use App\Models\ActivityParticipation;
use App\Models\CommitteeMembership;
use App\Models\OrderLine;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * @param  string|null  $id
     * @return View
     */
    public function show($id = null)
    {
        $user = $id == null ? Auth::user() : User::fromPublicId($id);

        if ($user == null) {
            abort(404);
        }

        $pastCommittees = $this->getPastMemberships($user, false);
        $pastSocieties = $this->getPastMemberships($user, true);
        $moneySpent = $this->getSpentMoney($user);
        $totalProducts = $this->getProductsPurchased($user);
        $totalSignups = $this->getTotalSignups($user);
        $achievements = $user->achievements;

        return view('users.profile.profile', ['user' => $user, 'pastcommittees' => $pastCommittees, 'pastsocieties' => $pastSocieties, 'spentmoney' => $moneySpent, 'signups' => $totalSignups, 'totalproducts' => $totalProducts, 'achievements' => $achievements]);
    }

    /**
     * @param  User  $user
     * @return Collection|CommitteeMembership[]
     */
    private function getPastMemberships($user, bool $with_societies)
    {
        return CommitteeMembership::onlyTrashed()
            ->with('committee')
            ->where('user_id', $user->id)
            ->get()
            ->where('committee.is_society', $with_societies);
    }

    private function getSpentMoney($user)
    {
        return OrderLine::query()->where('user_id', $user->id)->pluck('total_price')->sum();
    }

    private function getProductsPurchased($user)
    {
        return OrderLine::query()->where('user_id', $user->id)->pluck('units')->sum();
    }

    private function getTotalSignups($user): int
    {
        return ActivityParticipation::query()->where('user_id', $user->id)->count();
    }
}
