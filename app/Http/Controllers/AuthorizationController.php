<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Permission;
use App\Models\User;
use Redirect;
use Role;
use Session;

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
        if ($id == config('proto.rootrole')) {
            Session::flash('flash_message', 'This role can only be manually added in the database.');

            return Redirect::back();
        }

        /** @var Role $role */
        $role = Role::findOrFail($id);
        /** @var User $user */
        $user = User::findOrFail($request->user);

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
        if ($id == config('proto.rootrole')) {
            Session::flash('flash_message', 'This role can only be manually removed in the database.');

            return Redirect::back();
        }

        /** @var Role $role */
        $role = Role::findOrFail($id);
        /** @var User $user */
        $user = User::findOrFail($userId);
        $user->removeRole($role);

        // Call Herbert webhook to run check through all connected admins.
        // Will result in kick for users whose temporary admin powers were removed.

        //        disabled because protube is down
        //        Http::get(config('herbert.server').'/adminCheck');

        Session::flash('flash_message', '<strong>'.$role->name.'</strong> has been revoked from '.$user->name.'.');

        return Redirect::back();
    }
}
