<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Redirect;
use Session;

class EmailListController extends Controller
{
    /** @return View */
    public function create()
    {
        return view('emailadmin.editlist', ['list' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        EmailList::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'is_member_only' => $request->has('is_member_only'),
        ]);

        Session::flash('flash_message', 'Your list has been created!');

        return Redirect::route('email::admin');
    }

    /** @return View */
    public function edit($id)
    {
        return view('emailadmin.editlist', ['list' => EmailList::findOrFail($id)]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $list = EmailList::findOrFail($id);
        $list->fill([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'is_member_only' => $request->has('is_member_only'),
        ]);
        $list->save();

        Session::flash('flash_message', 'The list has been updated!');

        return Redirect::route('email::admin');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(int $id)
    {
        $list = EmailList::findOrFail($id);
        $list->delete();

        Session::flash('flash_message', 'The list has been deleted!');

        return Redirect::route('email::admin');
    }

    /**
     * @param string $type
     * @param User $user
     */
    public static function autoSubscribeToLists(string $type, User $user)
    {
        $lists = config('proto.' . $type);
        foreach ($lists as $list) {
            $list = EmailList::find($list);
            $list?->subscribe($user);
        }
    }

    /**
     * @param int $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function toggleSubscription(int $id)
    {
        $user = Auth::user();
        /** @var EmailList $list */
        $list = EmailList::findOrFail($id);

        if ($list->isSubscribed($user)) {
            if ($list->unsubscribe($user)) {
                Session::flash('flash_message', 'You have been unsubscribed to the list ' . $list->name . '.');

                return Redirect::route('user::dashboard');
            }
        } else {
            if ($list->is_member_only && !$user->is_member) {
                Session::flash('flash_message', 'This list is only for members.');

                return Redirect::route('user::dashboard');
            }
            if ($list->subscribe($user)) {
                Session::flash('flash_message', 'You have been subscribed to the list ' . $list->name . '.');

                return Redirect::route('user::dashboard');
            }
        }

        Session::flash('flash_message', 'Something went wrong toggling your subscription for ' . $list->name . '.');

        return Redirect::route('user::dashboard');
    }
}
