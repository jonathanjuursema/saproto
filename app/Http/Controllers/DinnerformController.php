<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Proto\Models\DinnerOrderLine;
use Proto\Models\Account;
use Proto\Models\Dinnerform;


use Session;
use Redirect;
use Response;

class DinnerformController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $dinnerformList = Dinnerform::all();

        return view('dinnerform.admin', ['dinnerformCurrent' => null, 'dinnerformList' => $dinnerformList]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $dinnerform = new Dinnerform();
        $dinnerform->restaurant = $request->restaurant;
        $dinnerform->description = $request->description;
        $dinnerform->start = strtotime($request->start);
        $dinnerform->end = strtotime($request->end);

        if ($dinnerform->end < $dinnerform->start) {
            Session::flash("flash_message", "You cannot let the dinner form close before it opens.");
            return Redirect::back();
        }

        $dinnerform->save();

        Session::flash("flash_message", "Your dinner form at '" . $dinnerform->restaurant . "' has been added.");
        return Redirect::route('homepage');

    }

    /**
     * Display the specified event.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $dinnerform = Dinnerform::fromPublicId($id);

        if ($dinnerform->isCurrent()) {
            return view('dinnerform.show', ['dinnerform' => $dinnerform]);
        } elseif ($dinnerform->isBoardMember(Auth::user())) {
            //SHOW ALL ORDERS OF A DINNERFORM
            $dinnerform->returnAllOrders();
        }
        else {
            Session::flash("flash_message", "Sorry, you can't order anymore, because food is already on its way");
            return Redirect::route('homepage');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $dinnerformCurrent = Dinnerform::findOrFail($id);
        $dinnerformList = Dinnerform::all();

        if($dinnerformCurrent != null) {
            return view('dinnerform.admin', ['dinnerformCurrent' => $dinnerformCurrent, 'dinnerformList' => $dinnerformList]);
        } else {
            return view('dinnerform.admin', ['dinnerformCurrent' => null, 'dinnerformList' => $dinnerformList]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $dinnerform = Dinnerform::findOrFail($id);

        $changed_important_details = $dinnerform->start != strtotime($request->start) || $dinnerform->end != strtotime($request->end) || $dinnerform->restaurant != $request->restaurant ? true : false;

        $dinnerform->restaurant = $request->restaurant;
        $dinnerform->start = strtotime($request->start);
        $dinnerform->end = strtotime($request->end);
        $dinnerform->description = $request->description;

        if ($dinnerform->end < $dinnerform->start) {
            Session::flash("flash_message", "You cannot let the dinnerform close before it opens.");
            return Redirect::back();
        }

        $dinnerform->save();

        if ($changed_important_details) {
            Session::flash("flash_message", "Your dinner form for '" . $dinnerform->restaurant . "' has been saved. You updated some important information. Don't forget to update your participants with this info!");
        } else {
            Session::flash("flash_message", "Your dinner form for '" . $dinnerform->restaurant . "' has been saved.");
        }

        return Redirect::route('homepage');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dinnerform = Dinnerform::findOrFail($id);

        Session::flash("flash_message", "The dinner form for '" . $dinnerform->restaurant . "' has been deleted.");

        if(URL::previous() != route('dinnerform::edit', ['id' => $dinnerform->id])) {
            $dinnerform->delete();
            return Redirect::back();
        } else {
            $dinnerform->delete();
            return Redirect::route('dinnerform::add');
        }
    }

    public function addOrder(Request $request)
    {
        $dinnerOrderLine = new DinnerOrderLine();
        $dinnerOrderLine->user_id = Auth::user()->id;
        $dinnerOrderLine->dinnerform_id = $request->id;
        $dinnerOrderLine->dish = $request->dish;
        $dinnerOrderLine->price = $request->price;

        if($dinnerOrderLine->dish == null or $dinnerOrderLine->price == null)
        {
            Session::flash("flash_message", "Please fill in both the dish(es) you want to order and the price of your order.");
            return Redirect::back();
        }

        $dinnerOrderLine->save();
        Session::flash("flash_message", "You have ordered " . $dinnerOrderLine->dish . " at a price of ".$dinnerOrderLine->price);
        return Redirect::route('homepage');
    }

    public function returnOrders($id){
        $dinnerform = Dinnerform::findorfail($id);
        $orders = $dinnerform->returnAllOrders();

        return view ('dinnerform.admin-inlcudes.orderlist', ['Orders' => $orders]);

    }
}
