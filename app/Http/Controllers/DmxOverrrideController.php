<?php

namespace App\Http\Controllers;

use App\Models\DmxFixture;
use App\Models\DmxOverride;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class DmxOverrrideController extends Controller
{
    public function index()
    {
        return view('dmx.override.index', [
            'overrides' => DmxOverride::getActiveSorted(),
            'upcoming_overrides' => DmxOverride::getUpcomingSorted(),
            'past_overrides' => DmxOverride::getPastSorted(),
        ]);
    }

    public function create(): Factory|View|Application|\Illuminate\View\View
    {
        return view('dmx.override.edit', ['override' => null, 'fixtures' => DmxFixture::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $fixtures = implode(',', $request->fixtures);
        $color = sprintf('%d,%d,%d,%d', $request->red, $request->green, $request->blue, $request->brightness);
        $start = strtotime($request->start);
        $end = strtotime($request->end);

        $override = DmxOverride::query()->create([
            'fixtures' => $fixtures,
            'color' => $color,
            'start' => $start,
            'end' => $end,
        ]);
        $override->save();

        Session::flash('flash_message', 'Override created.');

        return Redirect::route('dmx.overrides.edit', ['override' => $override]);
    }

    public function edit(DmxOverride $override): Factory|View|Application|\Illuminate\View\View
    {
        return view('dmx.override.edit', ['override' => $override,
            'fixtures' => DmxFixture::query()->orderBy('name')->get(), ]);
    }

    public function update(Request $request, DmxOverride $override): RedirectResponse
    {

        $fixtures = implode(',', $request->fixtures);
        $color = sprintf('%d,%d,%d,%d', $request->red, $request->green, $request->blue, $request->brightness);
        $start = strtotime($request->start);
        $end = strtotime($request->end);

        $override->update([
            'fixtures' => $fixtures,
            'color' => $color,
            'start' => $start,
            'end' => $end,
        ]);

        Session::flash('flash_message', 'Override updated.');

        return Redirect::route('dmx.overrides.edit', ['override' => $override]);
    }

    public function destroy(DmxOverride $override): RedirectResponse
    {
        Session::flash('flash_message', 'The override has been deleted.');
        $override->delete();

        return Redirect::route('dmx.overrides.index');
    }
}
