<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProTubeApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Permission;
use Role;

class AuthorizationController extends Controller
{
    /** @return View */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('authorization.overview', ['roles' => $roles, 'permissions' => $permissions]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function grant(Request $request, $id)
    {
        if ($id == Config::integer('proto.rootrole')) {
            Session::flash('flash_message', 'This role can only be manually added in the database.');

            return Redirect::back();
        }

        /** @var Role $role */
        $role = Role::findOrFail($id);
        /** @var User $user */
        $user = User::query()->findOrFail($request->user);

        if ($user->hasRole($role)) {
            Session::flash('flash_message', $user->name.' already has role: <strong>'.$role->name.'</strong>.');

            return Redirect::back();
        }

        $user->assignRole($role);

        Session::flash('flash_message', $user->name.' has been granted role: <strong>'.$role->name.'</strong>.');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @param  int  $userId
     * @return RedirectResponse
     */
    public function revoke(Request $request, $id, $userId)
    {
        if ($id == Config::integer('proto.rootrole')) {
            Session::flash('flash_message', 'This role can only be manually removed in the database.');

            return Redirect::back();
        }

        /** @var Role $role */
        $role = Role::findOrFail($id);
        /** @var User $user */
        $user = User::query()->findOrFail($userId);
        $user->removeRole($role);

        // Call Protube webhook to remove this user's admin rights
        ProTubeApiService::updateAdmin($user->id, false);

        Session::flash('flash_message', '<strong>'.$role->name.'</strong> has been revoked from '.$user->name.'.');

        return Redirect::back();
    }
}
