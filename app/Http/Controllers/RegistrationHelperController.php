<?php

namespace App\Http\Controllers;

use App\Enums\MembershipTypeEnum;
use App\Models\User;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationHelperController extends Controller
{
    /**
     * Display a list of pending members.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $search = $request->input('query');

        $users = User::query()->whereHas('member', static function ($q) {
            $q->type(MembershipTypeEnum::PENDING);
        });

        if ($search) {
            $users = $users->where(static function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('calling_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('utwente_username', 'LIKE', "%{$search}%")
                    ->orWhereHas('member', static function ($q) use ($search) {
                        $q->where('proto_username', 'LIKE', "%{$search}%");
                    });
            });
        }

        $users = $users->paginate(20);

        return view('users.admin.registration_helper.overview', ['users' => $users, 'query' => $search]);
    }

    /**
     * Show the user details for registration helper.
     *
     * @return View
     */
    public function details(int $id)
    {
        $user = User::query()->whereHas('member', static function ($q) {
            $q->type(MembershipTypeEnum::PENDING)->orWhere('updated_at', '>', Carbon::now()->subDay());
        })->findOrFail($id);
        $memberships = $user->getMemberships();

        return view('users.admin.registration_helper.details', ['user' => $user, 'memberships' => $memberships]);
    }
}
