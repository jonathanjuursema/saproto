<?php

namespace App\Http\Controllers;

use App\Models\Tempadmin;
use App\Models\User;
use App\Services\ProTubeApiService;
use Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TempAdminController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $tempadmins = Tempadmin::query()->where('end_at', '>', DB::raw('NOW()'))->orderBy('end_at', 'desc')->get();
        $pastTempadmins = Tempadmin::query()->where('end_at', '<=', DB::raw('NOW()'))->orderBy('end_at', 'desc')->take(10)->get();

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
        /** @var $tempAdminUser User */
        $tempAdminUser = User::query()->findOrFail($request->user_id);

        $tempadmin = new Tempadmin;
        $tempadmin->user()->associate($tempAdminUser);
        $tempadmin->creator()->associate(Auth::user());
        $tempadmin->start_at = date('Y-m-d H:i:s', strtotime($request->start_at));
        $tempadmin->end_at = date('Y-m-d H:i:s', strtotime($request->end_at));
        $tempadmin->save();

        ProTubeApiService::updateAdmin($tempAdminUser->id, $tempAdminUser->isTempadminLaterToday());

        return Redirect::route('tempadmins.index');
    }

    /**
     * @return View
     */
    public function edit(Tempadmin $tempadmin)
    {
        return view('tempadmin.edit', ['item' => $tempadmin, 'new' => false]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(Tempadmin $tempadmin, Request $request)
    {
        $tempadmin->start_at = date('Y-m-d H:i:s', strtotime($request->start_at));
        $tempadmin->end_at = date('Y-m-d H:i:s', strtotime($request->end_at));
        $tempadmin->save();

        ProTubeApiService::updateAdmin($tempadmin->user->id, $tempadmin->user->isTempadminLaterToday());

        return Redirect::route('tempadmins.index');
    }

    /**
     * @return RedirectResponse
     */
    public function make(int $id)
    {
        /** @var $user User */
        $user = User::query()->findOrFail($id);

        $tempAdmin = new Tempadmin;
        $tempAdmin->created_by = Auth::user()->id;
        $tempAdmin->start_at = Carbon::today();
        $tempAdmin->end_at = Carbon::tomorrow();
        $tempAdmin->user()->associate($user);
        $tempAdmin->save();

        ProTubeApiService::updateAdmin($user->id, $user->isTempadminLaterToday());

        return Redirect::back();
    }

    /**
     * @return RedirectResponse
     */
    public function end(int $id)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

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
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function endId(int $id)
    {
        /** @var Tempadmin $tempadmin */
        $tempadmin = Tempadmin::query()->findOrFail($id);

        if (Carbon::parse($tempadmin->start_at)->isFuture()) {
            $tempadmin->delete();
        } else {
            $tempadmin->end_at = Carbon::now()->subSeconds(1);
            $tempadmin->save();
        }

        ProTubeApiService::updateAdmin($tempadmin->user->id, $tempadmin->user->isTempadminLaterToday());

        return Redirect::back();
    }
}
