<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\StorageEntry;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return RedirectResponse|View
     */
    public function index()
    {
        $companies = Company::query()->where('on_carreer_page', true)->inRandomOrder()->get();
        if (count($companies) > 0) {
            return view('companies.list', ['companies' => $companies]);
        }

        Session::flash('flash_message', 'There is currently nothing to see on the companies page, but please check back real soon!');

        return Redirect::back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return RedirectResponse|View
     */
    public function indexMembercard()
    {
        $companies = Company::query()->where('on_membercard', true)->inRandomOrder()->get();
        if (count($companies) > 0) {
            return view('companies.listmembercard', ['companies' => $companies]);
        }

        Session::flash('flash_message', 'There are currently no promotions for Proto members, please check back real soon!');

        return Redirect::back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function adminIndex()
    {
        return view('companies.adminlist', ['companies' => Company::query()->orderBy('sort')->paginate(20)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('companies.edit', ['company' => null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->name;
        $company->url = $request->url;
        $company->excerpt = $request->excerpt;
        $company->description = $request->description;
        $company->on_carreer_page = $request->has('on_carreer_page');
        $company->in_logo_bar = $request->has('in_logo_bar');
        $company->membercard_excerpt = $request->membercard_excerpt;
        $company->membercard_long = $request->membercard_long;
        $company->on_membercard = $request->has('on_membercard');
        $company->sort = Company::query()->max('sort') + 1;

        if ($request->file('image')) {
            $file = new StorageEntry;
            $file->createFromFile($request->file('image'));
            $company->image()->associate($file);
        }

        $company->save();

        Session::flash('flash_message', "Your company '".$company->name."' has been added.");

        return Redirect::route('companies::admin');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        return view('companies.show', ['company' => Company::query()->findOrFail($id)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function showMembercard($id)
    {
        return view('companies.showmembercard', ['company' => Company::query()->findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $company = Company::query()->findOrFail($id);

        return view('companies.edit', ['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function update(Request $request, $id)
    {
        $company = Company::query()->findOrFail($id);
        $company->name = $request->name;
        $company->url = $request->url;
        $company->excerpt = $request->excerpt;
        $company->description = $request->description;
        $company->on_carreer_page = $request->has('on_carreer_page');
        $company->in_logo_bar = $request->has('in_logo_bar');
        $company->membercard_excerpt = $request->membercard_excerpt;
        $company->membercard_long = $request->membercard_long;
        $company->on_membercard = $request->has('on_membercard');

        if ($request->file('image')) {
            $file = new StorageEntry;
            $file->createFromFile($request->file('image'));
            $company->image()->associate($file);
        }

        $company->save();

        Session::flash('flash_message', "Your company '".$company->name."' has been edited.");

        return Redirect::route('companies::admin');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function orderUp($id)
    {
        $company = Company::query()->findOrFail($id);

        if ($company->sort <= 0) {
            abort(500);
        }

        $companyAbove = Company::query()->where('sort', $company->sort - 1)->first();

        $companyAbove->sort++;
        $companyAbove->save();

        $company->sort--;
        $company->save();

        return Redirect::route('companies::admin');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function orderDown($id)
    {
        $company = Company::query()->findOrFail($id);

        if ($company->sort >= Company::query()->count() - 1) {
            abort(500);
        }

        $companyAbove = Company::query()->where('sort', $company->sort + 1)->first();

        $companyAbove->sort--;
        $companyAbove->save();

        $company->sort++;
        $company->save();

        return Redirect::route('companies::admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $company = Company::query()->findOrFail($id);

        Session::flash('flash_message', "The company '".$company->name."' has been deleted.");
        $company->delete();

        return Redirect::route('companies::admin');
    }
}
