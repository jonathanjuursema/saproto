<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Account;
use App\Models\Product;
use Redirect;
use Session;

class AccountController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('omnomcom.accounts.index', ['accounts' => Account::orderBy('account_number', 'asc')->get()]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        /** @var Account $account */
        $account = Account::findOrFail($id);

        return view('omnomcom.accounts.show', ['account' => $account, 'products' => Product::where('account_id', $account->id)->paginate(10)]);
    }

    /** @return View */
    public function create()
    {
        return view('omnomcom.accounts.edit', ['account' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        /** @var Account $account */
        $account = Account::create($request->all());
        $account->save();

        Session::flash('flash_message', 'Account '.$account->account_number.' ('.$account->name.') created.');

        return Redirect::route('omnomcom::accounts::list');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        return view('omnomcom.accounts.edit', ['account' => Account::findOrFail($id)]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Account $account */
        $account = Account::findOrFail($id);
        $account->fill($request->all());
        $account->save();

        Session::flash('flash_message', 'Account '.$account->account_number.' ('.$account->name.') saved.');

        return Redirect::route('omnomcom::accounts::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var Account $account */
        $account = Account::findOrFail($id);

        if ($account->products->count() > 0) {
            Session::flash('flash_message', 'Could not delete account '.$account->account_number.' ('.$account->name.') since there are products associated with this account.');

            return Redirect::back();
        }

        Session::flash('flash_message', 'Account '.$account->account_number.' ('.$account->name.') deleted.');
        $account->delete();

        return Redirect::route('omnomcom::accounts::list');
    }

    /**
     * Display aggregated results of sales. Per product to value that has been sold in the specified period.
     *
     * @param  int  $account
     * @return View
     */
    public function showAggregation(Request $request, $account)
    {
        /** @var Account $account */
        $account = Account::findOrFail($account);

        return view('omnomcom.accounts.aggregation', [
            'aggregation' => $account->generatePeriodAggregation($request->start, $request->end),
            'start' => $request->start, 'end' => $request->end, 'account' => $account,
        ]);
    }

    /**
     * Display aggregated results of sales for OmNomCom products. Per product to value that has been sold in the specified period.
     *
     * @return View
     */
    public function showOmnomcomStatistics(Request $request)
    {
        if ($request->has('start') && $request->has('end')) {
            $account = Account::findOrFail(config('omnomcom.omnomcom-account'));

            return view('omnomcom.accounts.aggregation', [
                'aggregation' => $account->generatePeriodAggregation($request->start, $request->end),
                'start' => $request->start, 'end' => $request->end, 'account' => $account,
            ]);
        } else {
            return view('omnomcom.statistics.date-select', ['select_text' => 'Select a time range over which to aggregate OmNomCom product sales.']);
        }
    }
}
