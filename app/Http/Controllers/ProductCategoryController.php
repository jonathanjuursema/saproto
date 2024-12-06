<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('omnomcom.categories.index', ['categories' => ProductCategory::query()->withCount('products')->get()]);
    }

    /** @return View */
    public function create()
    {
        return view('omnomcom.categories.edit', ['category' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $category = ProductCategory::query()->create($request->all());
        $category->save();

        Session::flash('flash_message', 'Category '.$category->name.' created.');

        return Redirect::route('omnomcom::categories::index');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $category = ProductCategory::query()->findOrFail($id);

        return view('omnomcom.categories.edit', ['category' => $category]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var ProductCategory $category */
        $category = ProductCategory::query()->findOrFail($id);
        $category->fill($request->all());
        $category->save();

        Session::flash('flash_message', 'Category '.$category->name.' saved.');

        return Redirect::route('omnomcom::categories::index');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var ProductCategory $category */
        $category = ProductCategory::query()->findOrFail($id);

        Session::flash('flash_message', 'Category '.$category->name.' deleted.');
        $category->delete();

        return Redirect::route('omnomcom::categories::index');
    }
}
