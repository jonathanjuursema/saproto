<?php

namespace App\Http\Controllers;

use App\Models\Dinnerform;
use App\Models\DinnerformOrderline;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class DinnerformOrderlineController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function store(Request $request, int $id)
    {
        /** @var Dinnerform $dinnerform */
        $dinnerform = Dinnerform::query()->findOrFail($id);

        if ($dinnerform->hasOrdered()) {
            Session::flash('flash_message', 'You can only make one order per dinnerform!');

            return Redirect::back();
        }

        $validated = $request->validate([
            'order' => 'required|string',
            'price' => 'required|numeric',
            'helper' => 'nullable|boolean',
        ]);

        $helper = $request->has('helper') || $dinnerform->isHelping();

        DinnerformOrderline::query()->create([
            'description' => $validated['order'],
            'price' => $validated['price'],
            'user_id' => Auth::user()->id,
            'dinnerform_id' => $dinnerform->id,
            'helper' => $helper,
            'closed' => false,
        ]);

        Session::flash('flash_message', 'Your order has been saved!');

        return Redirect::back();
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function delete(int $id)
    {
        $dinnerOrderline = DinnerformOrderline::query()->findOrFail($id);
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

    public function edit(int $id): View|RedirectResponse
    {
        $dinnerOrderline = DinnerformOrderline::query()->findOrFail($id);
        if ($dinnerOrderline->closed) {
            Session::flash('flash_message', 'You cannot edit an order of a closed dinnerform!');

            return Redirect::back();
        }

        return view('dinnerform.admin-edit-order', ['dinnerformOrderline' => $dinnerOrderline]);
    }

    public function update(Request $request, int $id): View
    {
        $dinnerOrderline = DinnerformOrderline::query()->findOrFail($id);
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

        $dinnerform = Dinnerform::query()->findOrFail($dinnerOrderline->dinnerform_id);
        Session::flash('flash_message', 'Your order has been updated!');

        return view('dinnerform.admin', ['dinnerform' => $dinnerform, 'orderList' => $dinnerform->orderlines()->get()]);
    }
}
