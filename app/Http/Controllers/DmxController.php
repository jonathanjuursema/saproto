<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\DmxChannel;
use App\Models\DmxFixture;
use App\Models\DmxOverride;
use Redirect;
use Session;

class DmxController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('dmx.index', ['fixtures' => DmxFixture::orderBy('name', 'asc')->get()]);
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
        $fixture = DmxFixture::create($request->all());
        $fixture->save();

        foreach (range($fixture->channel_start, $fixture->channel_end) as $channel_id) {
            $channel = DmxChannel::find($channel_id);
            if (! $channel) {
                DmxChannel::create(['id' => $channel_id, 'name' => 'Unnamed Channel']);
            }
        }

        Session::flash('flash_message', sprintf('The new fixture %s has been stored.', $fixture->name));

        return Redirect::route('dmx::edit', ['id' => $fixture->id]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        return view('dmx.edit', ['fixture' => DmxFixture::findOrFail($id)]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $fixture = DmxFixture::findOrFail($id);
        $fixture->fill($request->except('channel_name', 'special_function'));
        $fixture->save();

        foreach ($request->channel_name as $channel_id => $channel_name) {
            $channel = DmxChannel::where('id', $channel_id)->first();
            $channel->name = $channel_name;
            $channel->save();
        }

        foreach ($request->special_function as $channel_id => $channel_function) {
            $channel = DmxChannel::where('id', $channel_id)->first();
            $channel->special_function = $channel_function;
            $channel->save();
        }

        Session::flash('flash_message', sprintf('The fixture %s has been updated.', $fixture->name));

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete($id)
    {
        /** @var DmxFixture $fixture */
        $fixture = DmxFixture::findOrFail($id);
        Session::flash('flash_message', sprintf('The fixture %s has been deleted.', $fixture->name));
        $fixture->delete();

        return Redirect::route('dmx::index');
    }

    /** @return View */
    public function overrideIndex()
    {
        return view('dmx.override.index', [
            'overrides' => DmxOverride::getActiveSorted(),
            'upcoming_overrides' => DmxOverride::getUpcomingSorted(),
            'past_overrides' => DmxOverride::getPastSorted(),
        ]);
    }

    /** @return View */
    public function overrideCreate()
    {
        return view('dmx.override.edit', ['override' => null, 'fixtures' => DmxFixture::orderBy('name', 'asc')->get()]);
    }

    /**
     * @return RedirectResponse
     */
    public function overrideStore(Request $request)
    {
        $fixtures = implode(',', $request->fixtures);
        $color = sprintf('%d,%d,%d,%d', $request->red, $request->green, $request->blue, $request->brightness);
        $start = strtotime($request->start);
        $end = strtotime($request->end);

        $override = DmxOverride::create([
            'fixtures' => $fixtures,
            'color' => $color,
            'start' => $start,
            'end' => $end,
        ]);
        $override->save();

        Session::flash('flash_message', 'Override created.');

        return Redirect::route('dmx::override::edit', ['id' => $override->id]);
    }

    /** @return View */
    public function overrideEdit($id)
    {
        return view('dmx.override.edit', ['override' => DmxOverride::findOrFail($id),
            'fixtures' => DmxFixture::orderBy('name', 'asc')->get(), ]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function overrideUpdate(Request $request, $id)
    {
        $override = DmxOverride::findOrFail($id);

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
        $override->save();

        Session::flash('flash_message', 'Override updated.');

        return Redirect::route('dmx::override::edit', ['id' => $override->id]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function overrideDelete($id)
    {
        $override = DmxOverride::findOrFail($id);
        Session::flash('flash_message', 'The override has been deleted.');
        $override->delete();

        return Redirect::route('dmx::override::index');
    }

    /** @return array */
    public function valueApi()
    {
        // Get the events.
        $events = CalendarController::returnGoogleCalendarEvents(config('proto.smartxp-google-timetable-id'), date('c', strtotime('last week')), date('c', strtotime('next week')));

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
            if (in_array($current_event['type'], config('dmx.lecture_types'))) {
                $preset = 'lecture';
            } else {
                $preset = 'tutorial';
            }
        }

        $channel_values = [];

        // Now we fill the preset channels.
        $preset_colors = (6 < date('G') && date('G') < 20 ? array_merge(config('dmx.colors')[$preset], [50]) : [0, 0, 0, 0]);
        foreach (DmxFixture::where('follow_timetable', true)->get() as $fixture) {
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
     * @param  DmxFixture  $fixture
     * @param  int[]  $channel_values
     * @param  int[]  $colors
     * @return int[]
     */
    private function setFixtureChannels($fixture, $channel_values, $colors)
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
