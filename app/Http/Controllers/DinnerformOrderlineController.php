<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Dinnerform;
use App\Models\DinnerformOrderline;
use Session;

class DinnerformOrderlineController extends Controller
{
    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function store(Request $request, $id)
    {
        $dinnerform = Dinnerform::findOrFail($id);

        if ($dinnerform->hasOrdered()) {
            Session::flash('flash_message', 'You can only make one order per dinnerform!');

            return Redirect::back();
        }

        $order = $request->input('order');
        $amount = $request->input('price');
        $helper = $request->has('helper') || $dinnerform->isHelping();

        $dinnerOrderline = DinnerformOrderline::create([
            'description' => $order,
            'price' => $amount,
            'user_id' => Auth::user()->id,
            'dinnerform_id' => $id,
            'helper' => $helper,
        ]);

        Session::flash('flash_message', 'Your order has been saved!');

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
        $dinnerOrderline = DinnerformOrderline::findOrFail($id);
        if ($dinnerOrderline->closed) {
            Session::flash('flash_message', 'You cannot delete an order of a closed dinnerform!');

            return Redirect::back();
        }
        if (! Auth::user() || Auth::user()->id !== $dinnerOrderline->user_id || ! $dinnerOrderline->dinnerform->isCurrent() || ! Auth::user()->can('tipcie')) {
            Session::flash('flash_message', 'You are not authorized to delete this order!');
            Redirect::back();
        }

        $dinnerOrderline->delete();
        Session::flash('flash_message', 'Your order has been deleted!');

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return View|RedirectResponse
     */
    public function edit($id)
    {
        $dinnerOrderline = DinnerformOrderline::findOrFail($id);
        if ($dinnerOrderline->closed) {
            Session::flash('flash_message', 'You cannot edit an order of a closed dinnerform!');

            return Redirect::back();
        }

        return view('dinnerform.admin-edit-order', ['dinnerformOrderline' => $dinnerOrderline]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function update(Request $request, $id)
    {
        $dinnerOrderline = DinnerformOrderline::findOrFail($id);
        if ($dinnerOrderline->closed) {
            $dinnerform = $dinnerOrderline->dinnerform;
            Session::flash('flash_message', 'You cannot update an order of a closed dinnerform!');

            return view('dinnerform.admin', ['dinnerform' => $dinnerform, 'orderList' => $dinnerform->orderlines()->get()]);
        }

        $order = $request->input('order');
        $amount = $request->input('price');
        $helper = $request->has('helper');
        $dinnerOrderline->update([
            'description' => $order,
            'price' => $amount,
            'user_id' => $dinnerOrderline->user_id,
            'helper' => $helper,
        ]);
        $dinnerOrderline->save();
        $dinnerform = Dinnerform::findOrFail($dinnerOrderline->dinnerform_id);
        Session::flash('flash_message', 'Your order has been updated!');

        return view('dinnerform.admin', ['dinnerform' => $dinnerform, 'orderList' => $dinnerform->orderlines()->get()]);
    }
}
