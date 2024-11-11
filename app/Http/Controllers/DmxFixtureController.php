<?php

namespace App\Http\Controllers;

use App\Models\DmxChannel;
use App\Models\DmxFixture;
use App\Models\DmxOverride;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class DmxFixtureController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('dmx.index', ['fixtures' => DmxFixture::query()->orderBy('name', 'asc')->get()]);
    }

    /** @return View */
    public function create()
    {
        return view('dmx.edit', ['fixture' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $fixture = DmxFixture::query()->create($request->all());
        $fixture->save();

        foreach (range($fixture->channel_start, $fixture->channel_end) as $channel_id) {
            $channel = DmxChannel::query()->find($channel_id);
            if (! $channel) {
                DmxChannel::query()->create(['id' => $channel_id, 'name' => 'Unnamed Channel']);
            }
        }

        Session::flash('flash_message', sprintf('The new fixture %s has been stored.', $fixture->name));

        return Redirect::route('dmx.fixtures.edit', ['fixture' => $fixture]);
    }

    /**
     * @return View
     */
    public function edit(DmxFixture $fixture)
    {
        return view('dmx.edit', ['fixture' => $fixture]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(Request $request, DmxFixture $fixture)
    {
        $fixture->fill($request->except('channel_name', 'special_function'));
        $fixture->save();

        foreach ($request->channel_name as $channel_id => $channel_name) {
            $channel = DmxChannel::query()->where('id', $channel_id)->first();
            $channel->name = $channel_name;
            $channel->save();
        }

        foreach ($request->special_function as $channel_id => $channel_function) {
            $channel = DmxChannel::query()->where('id', $channel_id)->first();
            $channel->special_function = $channel_function;
            $channel->save();
        }

        Session::flash('flash_message', sprintf('The fixture %s has been updated.', $fixture->name));

        return Redirect::back();
    }

    /**
     * @return RedirectResponse
     */
    public function destroy(DmxFixture $fixture)
    {
        Session::flash('flash_message', sprintf('The fixture %s has been deleted.', $fixture->name));
        $fixture->delete();

        return Redirect::route('dmx.fixtures.index');
    }

    public function valueApi(): array
    {
        // Get the events.
        $events = CalendarController::returnGoogleCalendarEvents(config('proto.google-calendar.smartxp-id'), date('c', strtotime('last week')), date('c', strtotime('next week')));

        // Determine if any event is currently going on.
        $current_event = null;
        foreach ($events as $event) {
            if ($event['current']) {
                $current_event = $event;
            }
        }

        // Determine what preset to use.
        $preset = 'free';
        if ($current_event !== null) {
            $preset = in_array($current_event['type'], config('dmx.lecture_types')) ? 'lecture' : 'tutorial';
        }

        $channel_values = [];

        // Now we fill the preset channels.
        $preset_colors = (date('G') > 6 && date('G') < 20 ? array_merge(config('dmx.colors')[$preset], [50]) : [0, 0, 0, 0]);
        foreach (DmxFixture::query()->where('follow_timetable', true)->get() as $fixture) {
            $channel_values = self::setFixtureChannels($fixture, $channel_values, $preset_colors);
        }

        // And we apply the overrides.
        foreach (DmxOverride::getActiveSorted()->reverse() as $override) {
            if (! $override->active() && ! $override->justOver()) {
                continue;
            }

            foreach ($override->getFixtures() as $fixture) {
                if ($override->justOver() && $fixture->follow_timetable) {
                    continue;
                }

                $colors = ($override->justOver() && ! $fixture->follow_timetable ? [0, 0, 0, 0] : $override->colorArray());
                $channel_values = self::setFixtureChannels($fixture, $channel_values, $colors);
            }
        }

        return $channel_values;
    }

    /**
     * @param  int[]  $channel_values
     * @param  int[]  $colors
     * @return int[]
     */
    private function setFixtureChannels(DmxFixture $fixture, array $channel_values, array $colors): array
    {
        // Set red
        foreach ($fixture->getChannels('red') as $channel) {
            $channel_values[$channel->id] = $colors[0];
        }

        // Set green
        foreach ($fixture->getChannels('green') as $channel) {
            $channel_values[$channel->id] = $colors[1];
        }

        // Set blue
        foreach ($fixture->getChannels('blue') as $channel) {
            $channel_values[$channel->id] = $colors[2];
        }

        // Set brightness
        foreach ($fixture->getChannels('brightness') as $channel) {
            $channel_values[$channel->id] = $colors[3];
        }

        return $channel_values;
    }
}
