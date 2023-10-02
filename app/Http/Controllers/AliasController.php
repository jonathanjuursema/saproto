<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Alias;
use App\Models\User;
use Redirect;
use Session;

class AliasController extends Controller
{
    /** @return View|RedirectResponse */
    public function index()
    {
        $aliases = Alias::orderBy('alias', 'asc')->get();

        if ($aliases->count() > 0) {
            $data = [];
            foreach ($aliases as $alias) {
                $data[$alias->alias][] = $alias;
            }

            return view('aliases.index', ['aliases' => $data]);
        } else {
            return Redirect::route('alias::add');
        }
    }

    /** @return View */
    public function create()
    {
        return view('aliases.add');
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->input('destination') != '') {
            $alias = Alias::create([
                'alias' => $request->input('alias'),
                'destination' => $request->input('destination'),
            ]);
            $alias->save();

            Session::flash('flash_message', 'Destination added to alias.');
        } elseif ($request->input('user') != '') {
            /** @var User $user */
            $user = User::findOrFail($request->input('user'));
            $alias = Alias::create([
                'alias' => $request->input('alias'),
                'user_id' => $user->id,
            ]);
            $alias->save();

            Session::flash('flash_message', 'User added to alias.');
        } else {
            Session::flash('flash_message', 'No action performed.');
        }

        return Redirect::route('alias::index');
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $from = $request->input('from');
        $into = $request->input('into');

        $affected = DB::table('alias')->where('alias', $from)->update([
            'alias' => $into,
        ]);

        if ($affected > 0) {
            Session::flash('flash_message', 'Renamed '.$from.' into '.$into.'.');
        } else {
            Session::flash('flash_message', 'No such alias ('.$from.').');
        }

        return Redirect::back();
    }

    /**
     * @param  mixed  $id_or_alias
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id_or_alias)
    {
        $alias = Alias::find($id_or_alias);

        if ($alias) {
            $alias->delete();
            Session::flash('flash_message', 'Entry deleted.');

            return Redirect::back();
        }

        $affected = DB::table('alias')->where('alias', $id_or_alias)->delete();
        if ($affected > 0) {
            Session::flash('flash_message', 'Deleted alias <strong>'.$id_or_alias.'</strong> with '.$affected.' destinations.');
        } else {
            Session::flash('flash_message', 'No such alias ('.$id_or_alias.').');
        }

        return Redirect::back();
    }
}
