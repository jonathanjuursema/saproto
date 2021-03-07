<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;
use Proto\Models\CommitteeMembership;

use Proto\Models\User;

use Auth;

class UserProfileController extends Controller
{

    /**
     * Display the profile for a specific user.
     *
     * @param  int $id
     * @return \Illuminate\View\View
     */
    public function show($id = null)
    {
        if ($id == null) {
            $user = Auth::user();
        } else {
            $user = User::fromPublicId($id);
        }

        if ($user == null) {
            abort(404);
        }

        $pastCommittees = $this->getGroups($user, false);
        $pastSocieties = $this->getGroups($user, true);

        return view('users.profile.profile', ['user' => $user, 'pastcommittees' => $pastCommittees, 'pastsocieties' => $pastSocieties]);
    }

    private function getGroups($user, $getsocieties) {
        return CommitteeMembership::withTrashed()
            ->with('committee')
            ->where('user_id', $user->id)
            ->whereNotIn('id', $user->committees->pluck('pivot.id'))
            ->get()
            ->where('committee.is_society', $getsocieties);
    }

}
