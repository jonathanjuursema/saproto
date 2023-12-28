<?php

namespace App\Http\Controllers;

use App\Models\Tempadmin;
use App\Models\User;
use App\Services\ProTubeApiService;
use Auth;
use Carbon;
use DB;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Redirect;

class TempAdminController extends Controller
{
    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function make($id)
    {
        /** @var User */
        $user = User::findOrFail($id);

        $tempAdmin = new Tempadmin();
        $tempAdmin->created_by = Auth::user()->id;
        $tempAdmin->start_at = Carbon::today();
        $tempAdmin->end_at = Carbon::tomorrow();
        $tempAdmin->user()->associate($user);
        $tempAdmin->save();

        ProTubeApiService::updateAdmin($user->id, $user->isTempadminLaterToday());

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function end($id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        foreach ($user->tempadmin as $tempadmin) {
            if (Carbon::now()->between(Carbon::parse($tempadmin->start_at), Carbon::parse($tempadmin->end_at))) {
                $tempadmin->end_at = Carbon::now()->subSeconds(1);
                $tempadmin->save();
            }
        }

        ProTubeApiService::updateAdmin($user->id, $user->isTempadminLaterToday());

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function endId($id)
    {
        /** @var Tempadmin $tempadmin */
        $tempadmin = Tempadmin::findOrFail($id);

        if (Carbon::parse($tempadmin->start_at)->isFuture()) {
            $tempadmin->delete();
        } else {
            $tempadmin->end_at = Carbon::now()->subSeconds(1);
            $tempadmin->save();
        }

        ProTubeApiService::updateAdmin($tempadmin->user->id, $tempadmin->user->isTempadminLaterToday());

        return Redirect::back();
    }

    /**
     * @return View
     */
    public function index()
    {
        $tempadmins = Tempadmin::where('end_at', '>', DB::raw('NOW()'))->orderBy('end_at', 'desc')->get();
        $pastTempadmins = Tempadmin::where('end_at', '<=', DB::raw('NOW()'))->orderBy('end_at', 'desc')->take(10)->get();

        return view('tempadmin.list', ['tempadmins' => $tempadmins, 'pastTempadmins' => $pastTempadmins]);
    }

    /** @return View */
    public function create()
    {
        return view('tempadmin.edit', ['tempadmin' => null, 'new' => true]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        /** @var User */
        $tempAdminUser = User::findOrFail($request->user_id);

        $tempadmin = new Tempadmin();
        $tempadmin->user()->associate($tempAdminUser);
        $tempadmin->creator()->associate(Auth::user());
        $tempadmin->start_at = date('Y-m-d H:i:s', strtotime($request->start_at));
        $tempadmin->end_at = date('Y-m-d H:i:s', strtotime($request->end_at));
        $tempadmin->save();

        ProTubeApiService::updateAdmin($tempAdminUser->id, $tempAdminUser->isTempadminLaterToday());

        return Redirect::route('tempadmin::index');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $tempadmin = Tempadmin::findOrFail($id);

        return view('tempadmin.edit', ['item' => $tempadmin, 'new' => false]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        /** @var Tempadmin $tempadmin */
        $tempadmin = Tempadmin::findOrFail($id);
        $tempadmin->start_at = date('Y-m-d H:i:s', strtotime($request->start_at));
        $tempadmin->end_at = date('Y-m-d H:i:s', strtotime($request->end_at));
        $tempadmin->save();

        ProTubeApiService::updateAdmin($tempadmin->user->id, $tempadmin->user->isTempadminLaterToday());

        return Redirect::route('tempadmin::index');
    }
}
