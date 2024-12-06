<?php

namespace App\Http\Controllers;

use App\Models\PasswordEntry;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Permission;

class PasswordController extends Controller
{
    /** @return View */
    public function getAuth()
    {
        return view('passwordstore.reauth');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function postAuth(Request $request)
    {
        if (AuthController::verifyCredentials(Auth::user()->email, $request->password)) {
            $request->session()->put('passwordstore-verify', strtotime('+10 minutes'));
            Session::flash('flash_message', 'You can access this tool for 10 minutes.');

            return Redirect::route('passwordstore::index');
        }

        Session::flash('flash_message', 'Wrong password.');

        return Redirect::route('passwordstore::auth');
    }

    /**
     * @return RedirectResponse|View
     */
    public function index(Request $request)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth();
        }

        return view('passwordstore.index', ['passwords' => PasswordEntry::query()->orderBy('permission_id', 'asc')->orderBy('description', 'asc')->get()]);
    }

    /**
     * @return RedirectResponse|View
     */
    public function create(Request $request)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth();
        }

        return view('passwordstore.edit', ['password' => null, 'type' => $request->get('type')]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth();
        }

        $permission = Permission::findOrFail($request->get('permission_id'));

        if (! Auth::user()->can($permission->name)) {
            Session::flash('flash_message', 'You are not allowed to set this permission for a password.');

            return Redirect::back();
        }

        if ($request->get('type') == 'password') {
            PasswordEntry::query()->create([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => Crypt::encrypt($request->get('username')),
                'password' => Crypt::encrypt($request->get('password')),
                'url' => ($request->get('url') == '' ? null : $request->get('url')),
                'note' => Crypt::encrypt($request->get('note')),
            ]);
            Session::flash('flash_message', 'Password saved.');

            return Redirect::route('passwordstore::index');
        }

        if ($request->get('type') == 'note') {
            PasswordEntry::query()->create([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => null,
                'password' => null,
                'url' => null,
                'note' => Crypt::encrypt($request->get('note')),
            ]);
            Session::flash('flash_message', 'Note saved.');

            return Redirect::route('passwordstore::index');
        }

        Session::flash('flash_message', 'Invalid input.');

        return Redirect::route('passwordstore::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse|View
     */
    public function edit(Request $request, $id)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth();
        }

        /** @var PasswordEntry $password */
        $password = PasswordEntry::query()->findOrFail($id);
        if (! $password->canAccess(Auth::user())) {
            Session::flash('flash_message', 'You are not allowed to edit this entry.');

            return Redirect::route('passwordstore::index');
        }

        return view('passwordstore.edit', ['password' => $password, 'type' => ($password->password == null ? 'note' : 'password')]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth();
        }

        /** @var PasswordEntry $password */
        $password = PasswordEntry::query()->findOrFail($id);

        if (! $password->canAccess(Auth::user())) {
            Session::flash('flash_message', 'You are not allowed to edit this entry.');

            return Redirect::route('passwordstore::index');
        }

        $permission = Permission::findOrFail($request->get('permission_id'));

        if (! Auth::user()->can($permission->name)) {
            Session::flash('flash_message', 'You are not allowed to set this permission for a password.');

            return Redirect::back();
        }

        if ($request->get('type') == 'password') {
            $password->fill([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => Crypt::encrypt($request->get('username')),
                'password' => Crypt::encrypt($request->get('password')),
                'url' => ($request->get('url') == '' ? null : $request->get('url')),
                'note' => Crypt::encrypt($request->get('note')),
            ]);
            $password->save();
            Session::flash('flash_message', 'Password saved.');

            return Redirect::route('passwordstore::index');
        }

        if ($request->get('type') == 'note') {
            $password->fill([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => null,
                'password' => null,
                'url' => null,
                'note' => Crypt::encrypt($request->get('note')),
            ]);
            $password->save();
            Session::flash('flash_message', 'Note saved.');

            return Redirect::route('passwordstore::index');
        }

        Session::flash('flash_message', 'Invalid input.');

        return Redirect::route('passwordstore::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth();
        }

        $password = PasswordEntry::query()->findOrFail($id);
        if (! $password->canAccess(Auth::user())) {
            Session::flash('flash_message', 'You are not allowed to delete this entry.');

            return Redirect::route('passwordstore::index');
        }

        $password->delete();

        Session::flash('flash_message', 'Password entry deleted.');

        return Redirect::route('passwordstore::index');
    }

    private function extraVerification(Request $request): bool
    {
        if (! $request->session()->has('passwordstore-verify')) {
            return false;
        }

        $verify = $request->session()->get('passwordstore-verify');
        if ($verify < date('U')) {
            $request->session()->forget('passwordstore-verify');

            return false;
        }

        return true;
    }

    /**
     * @return RedirectResponse
     */
    private function forwardToAuth()
    {
        Session::flash('flash_message', 'You need to enter your password again, in order to access this feature.');

        return Redirect::route('passwordstore::auth');
    }
}
