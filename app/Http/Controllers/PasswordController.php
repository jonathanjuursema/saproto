<?php

namespace Proto\Http\Controllers;

use Auth;
use Crypt;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Permission;
use Proto\Models\PasswordEntry;
use Redirect;

class PasswordController extends Controller
{
    /** @return View */
    public function getAuth()
    {
        return view('passwordstore.reauth');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function postAuth(Request $request)
    {
        if (AuthController::verifyCredentials(Auth::user()->email, $request->password)) {
            $request->session()->put('passwordstore-verify', strtotime('+10 minutes'));
            $request->session()->flash('flash_message', 'You can access this tool for 10 minutes.');
            return Redirect::route('passwordstore::index');
        } else {
            $request->session()->flash('flash_message', 'Wrong password.');
            return Redirect::route('passwordstore::auth');
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function index(Request $request)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth($request);
        }

        return view('passwordstore.index', ['passwords' => PasswordEntry::orderBy('permission_id', 'asc')->orderBy('description', 'asc')->get()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function create(Request $request)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth($request);
        }

        return view('passwordstore.edit', ['password' => null, 'type' => $request->get('type')]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth($request);
        }

        $permission = Permission::findOrFail($request->get('permission_id'));

        if (! Auth::user()->can($permission->name)) {
            $request->session()->flash('flash_message', 'You are not allowed to set this permission for a password.');
            return Redirect::back();
        }

        if ($request->get('type') == 'password') {
            PasswordEntry::create([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => Crypt::encrypt($request->get('username')),
                'password' => Crypt::encrypt($request->get('password')),
                'url' => ($request->get('url') == '' ? null : $request->get('url')),
                'note' => Crypt::encrypt($request->get('note')),
            ]);

            $request->session()->flash('flash_message', 'Password saved.');
            return Redirect::route('passwordstore::index');
        } elseif ($request->get('type') == 'note') {
            PasswordEntry::create([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => null,
                'password' => null,
                'url' => null,
                'note' => Crypt::encrypt($request->get('note')),
            ]);

            $request->session()->flash('flash_message', 'Note saved.');
            return Redirect::route('passwordstore::index');
        }

        $request->session()->flash('flash_message', 'Invalid input.');
        return Redirect::route('passwordstore::index');
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|View
     */
    public function edit(Request $request, $id)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth($request);
        }

        /** @var PasswordEntry $password */
        $password = PasswordEntry::findOrFail($id);
        if (! $password->canAccess(Auth::user())) {
            $request->session()->flash('flash_message', 'You are not allowed to edit this entry.');
            return Redirect::route('passwordstore::index');
        }
        return view('passwordstore.edit', ['password' => $password, 'type' => ($password->password == null ? 'note' : 'password')]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth($request);
        }

        /** @var PasswordEntry $password */
        $password = PasswordEntry::findOrFail($id);

        if (! $password->canAccess(Auth::user())) {
            $request->session()->flash('flash_message', 'You are not allowed to edit this entry.');
            return Redirect::route('passwordstore::index');
        }

        $permission = Permission::findOrFail($request->get('permission_id'));

        if (! Auth::user()->can($permission->name)) {
            $request->session()->flash('flash_message', 'You are not allowed to set this permission for a password.');
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

            $request->session()->flash('flash_message', 'Password saved.');
            return Redirect::route('passwordstore::index');
        } elseif ($request->get('type') == 'note') {
            $password->fill([
                'permission_id' => $permission->id,
                'description' => $request->get('description'),
                'username' => null,
                'password' => null,
                'url' => null,
                'note' => Crypt::encrypt($request->get('note')),
            ]);

            $password->save();

            $request->session()->flash('flash_message', 'Note saved.');
            return Redirect::route('passwordstore::index');
        }

        $request->session()->flash('flash_message', 'Invalid input.');
        return Redirect::route('passwordstore::index');
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        if (! $this->extraVerification($request)) {
            return $this->forwardToAuth($request);
        }

        $password = PasswordEntry::findOrFail($id);
        if (! $password->canAccess(Auth::user())) {
            $request->session()->flash('flash_message', 'You are not allowed to delete this entry.');
            return Redirect::route('passwordstore::index');
        }
        $password->delete();

        $request->session()->flash('flash_message', 'Password entry deleted.');
        return Redirect::route('passwordstore::index');
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function extraVerification(Request $request)
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
     * @param Request $request
     * @return RedirectResponse
     */
    private function forwardToAuth(Request $request)
    {
        $request->session()->flash('flash_message', 'You need to enter your password again, in order to access this feature.');
        return Redirect::route('passwordstore::auth');
    }
}
