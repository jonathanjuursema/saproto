<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Proto\Models\Permission;
use Proto\Models\Role;
use Proto\Models\User;
use Redirect;

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
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function grant(Request $request, $id)
    {
        if ($id == config('proto.rootrole')) {
            $request->session()->flash('flash_message', 'This role can only be manually added in the database.');
            return Redirect::back();
        }

        /** @var Role $role */
        $role = Role::findOrFail($id);
        /** @var User $user */
        $user = User::findOrFail($request->user);

        $user->roles()->attach($role->id);

        $request->session()->flash('flash_message', $user->name.' has been granted <strong>'.$role->name.'</strong>.');
        return Redirect::back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $userId
     * @return RedirectResponse
     */
    public function revoke(Request $request, $id, $userId)
    {
        if ($id == config('proto.rootrole')) {
            $request->session()->flash('flash_message', 'This role can only be manually removed in the database.');
            return Redirect::back();
        }

        /** @var Role $role */
        $role = Role::findOrFail($id);
        /** @var User $user */
        $user = User::findOrFail($userId);
        $user->roles()->detach($role->id);

        // Call Herbert webhook to run check through all connected admins.
        // Will result in kick for users whose temporary admin powers were removed.
        file_get_contents(config('herbert.server').'/adminCheck');

        $request->session()->flash('flash_message', '<strong>'.$role->name.'</strong> has been revoked from '.$user->name.'.');
        return Redirect::back();
    }
}
