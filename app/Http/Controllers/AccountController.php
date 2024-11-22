<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AccountController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('omnomcom.accounts.index', ['accounts' => Account::query()->orderBy('account_number')->get()]);
    }

    public function show(int $id): View
    {
        /** @var Account $account */
        $account = Account::query()->findOrFail($id);

        return view('omnomcom.accounts.show', ['account' => $account, 'products' => Product::query()->where('account_id', $account->id)->paginate(10)]);
    }

    public function create(): View
    {
        return view('omnomcom.accounts.edit', ['account' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Account $account */
        $account = Account::query()->create($request->all());
        $account->save();

        Session::flash('flash_message', 'Account '.$account->account_number.' ('.$account->name.') created.');

        return Redirect::route('omnomcom::accounts::index');
    }

    public function edit(int $id): View
    {
        return view('omnomcom.accounts.edit', ['account' => Account::query()->findOrFail($id)]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        /** @var Account $account */
        $account = Account::query()->findOrFail($id);
        $account->fill($request->all());
        $account->save();

        Session::flash('flash_message', 'Account '.$account->account_number.' ('.$account->name.') saved.');

        return Redirect::route('omnomcom::accounts::index');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(int $id)
    {
        /** @var Account $account */
        $account = Account::query()->findOrFail($id);

        if ($account->products->count() > 0) {
            Session::flash('flash_message', 'Could not delete account '.$account->account_number.' ('.$account->name.') since there are products associated with this account.');

            return Redirect::back();
        }

        Session::flash('flash_message', 'Account '.$account->account_number.' ('.$account->name.') deleted.');
        $account->delete();

        return Redirect::route('omnomcom::accounts::index');
    }

    /**
     * Display aggregated results of sales. Per product to value that has been sold in the specified period.
     */
    public function showAggregation(Request $request, int $account): View
    {
        /** @var Account $account */
        $account = Account::query()->findOrFail($account);

        return view('omnomcom.accounts.aggregation', [
            'aggregation' => $account->generatePeriodAggregation($request->start, $request->end),
            'start' => $request->start, 'end' => $request->end, 'account' => $account,
        ]);
    }

    /**
     * Display aggregated results of sales for OmNomCom products. Per product to value that has been sold in the specified period.
     */
    public function showOmnomcomStatistics(Request $request): View
    {
        if ($request->has('start') && $request->has('end')) {
            $account = Account::query()->findOrFail(Config::integer('omnomcom.omnomcom-account'));

            return view('omnomcom.accounts.aggregation', [
                'aggregation' => $account->generatePeriodAggregation($request->start, $request->end),
                'start' => $request->start, 'end' => $request->end, 'account' => $account,
            ]);
        }

        return view('omnomcom.statistics.date-select', ['select_text' => 'Select a time range over which to aggregate OmNomCom product sales.']);
    }
}
