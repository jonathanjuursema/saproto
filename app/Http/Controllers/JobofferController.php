<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Joboffer;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class JobofferController extends Controller
{
    /** @return View */
    public function index()
    {
        $companies = Company::query()->has('joboffers')->get();

        return view('companies.joboffers.list', ['companies' => $companies]);
    }

    /** @return View */
    public function adminIndex()
    {
        $joboffers = Joboffer::all();

        return view('companies.joboffers.adminlist', ['joboffers' => $joboffers]);
    }

    /** @return View */
    public function create()
    {
        $companies = Company::all();

        return view('companies.joboffers.edit', ['joboffer' => null, 'companies' => $companies]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $description = $request->description;
        if ($description == '') {
            $description = null;
        }

        $redirect_url = $request->redirect_url;
        if ($redirect_url == '') {
            $redirect_url = null;
        }

        if ($description == null && $redirect_url == null) {
            Session::flash('flash_message', 'Please enter a description or redirect url.');

            return Redirect::back();
        }

        $joboffer = Joboffer::query()->create($request->all());
        $joboffer->save();

        return Redirect::route('joboffers::admin');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $joboffer = Joboffer::query()->findOrFail($id);

        return view('companies.joboffers.show', ['joboffer' => $joboffer]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $joboffer = Joboffer::query()->findOrFail($id);
        $companies = Company::all();

        return view('companies.joboffers.edit', ['joboffer' => $joboffer, 'companies' => $companies]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Joboffer $joboffer */
        $joboffer = Joboffer::query()->findOrFail($id);
        $joboffer->title = $request->title;
        $joboffer->description = $request->description;
        $joboffer->redirect_url = $request->redirect_url;
        $joboffer->save();

        Session::flash('flash_message', 'Job offer has been updated.');

        return Redirect::route('joboffers::admin');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $joboffer = Joboffer::query()->findOrFail($id);
        $joboffer->delete();

        Session::flash('flash_message', 'The job offer has been deleted.');

        return Redirect::back();
    }
}
