<?php

namespace Proto\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Proto\Models\Dinnerform;
use Session;

class DinnerformController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $dinnerformList = Dinnerform::all()->sortByDesc('end');

        return view('dinnerform.admin', ['dinnerformCurrent' => null, 'dinnerformList' => $dinnerformList]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->end < $request->start) {
            Session::flash('flash_message', 'You cannot let the dinner form close before it opens.');

            return Redirect::back();
        }

        $dinnerform = Dinnerform::create([
            'restaurant'  => $request->restaurant,
            'description' => $request->description,
            'url'         => $request->url,
            'start'       => strtotime($request->start),
            'end'         => strtotime($request->end),
        ]);

        Session::flash('flash_message', "Your dinner form at '".$dinnerform->restaurant."' has been added.");

        return Redirect::route('dinnerform::add');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);

        if ($dinnerform->isCurrent()) {
            return Redirect::away($dinnerform->url);
        } else {
            Session::flash('flash_message', "Sorry, you can't order anymore, food is already on its way");

            return Redirect::route('homepage');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $dinnerformCurrent = Dinnerform::findOrFail($id);
        $dinnerformList = Dinnerform::all()->sortByDesc('end');

        return view('dinnerform.admin', ['dinnerformCurrent' => $dinnerformCurrent, 'dinnerformList' => $dinnerformList]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if ($request->end < $request->start) {
            Session::flash('flash_message', 'You cannot let the dinnerform close before it opens.');

            return Redirect::back();
        }

        $dinnerform = Dinnerform::findOrFail($id);

        $changed_important_details = $dinnerform->start->timestamp != strtotime($request->start) || $dinnerform->end->timestamp != strtotime($request->end) || $dinnerform->restaurant != $request->restaurant;

        $dinnerform->update([
            'restaurant'  => $request->restaurant,
            'start'       => strtotime($request->start),
            'end'         => strtotime($request->end),
            'description' => $request->description,
        ]);

        if ($changed_important_details) {
            Session::flash('flash_message', "Your dinner form for '".$dinnerform->restaurant."' has been saved. You updated some important information. Don't forget to update your participants with this info!");
        } else {
            Session::flash('flash_message', "Your dinner form for '".$dinnerform->restaurant."' has been saved.");
        }

        return Redirect::route('dinnerform::add');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);

        Session::flash('flash_message', "The dinner form for '".$dinnerform->restaurant."' has been deleted.");

        $dinnerform->delete();

        if (URL::previous() != route('dinnerform::edit', ['id' => $dinnerform->id])) {
            return Redirect::back();
        } else {
            return Redirect::route('dinnerform::add');
        }
    }

    /**
     * Close the specified resource by changing the end time to the current time.
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function close($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);
        $dinnerform->end = Carbon::now();
        $dinnerform->save();

        return Redirect::route('dinnerform::add');
    }
}
