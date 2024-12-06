<?php

namespace App\Http\Controllers;

use App\Models\Alias;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AliasController extends Controller
{
    /** @return View|RedirectResponse */
    public function index()
    {
        $aliases = Alias::query()->orderBy('alias', 'asc')->get();

        if ($aliases->count() > 0) {
            $data = [];
            foreach ($aliases as $alias) {
                $data[$alias->alias][] = $alias;
            }

            return view('aliases.index', ['aliases' => $data]);
        }

        return Redirect::route('alias::create');
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
            $alias = Alias::query()->create([
                'alias' => $request->input('alias'),
                'destination' => $request->input('destination'),
            ]);
            $alias->save();

            Session::flash('flash_message', 'Destination added to alias.');
        } elseif ($request->input('user') != '') {
            /** @var User $user */
            $user = User::query()->findOrFail($request->input('user'));
            $alias = Alias::query()->create([
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
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, mixed $id_or_alias)
    {
        $alias = Alias::query()->find($id_or_alias);

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
