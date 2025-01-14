<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventCategoryRequest;
use App\Models\EventCategory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class EventCategoryController extends Controller
{
    public function create()
    {
        return view('event.categories', ['cur_category' => null]);
    }

    public function store(EventCategoryRequest $request)
    {
        $category = EventCategory::query()->create($request->validated());

        Session::flash('flash_message', 'The category '.$category->name.' has been created.');

        return Redirect::route('event::categories.edit', ['category' => $category]);
    }

    public function edit(EventCategory $category)
    {
        return view('event.categories', ['cur_category' => $category]);
    }

    public function update(EventCategoryRequest $request, EventCategory $category)
    {
        $category->update($request->validated());

        Session::flash('flash_message', 'The category '.$category->name.' has been updated.');

        return Redirect::route('event::categories.edit', ['category' => $category]);
    }

    public function destroy(EventCategory $category)
    {
        $category->events()->update(['category_id' => null]);
        $category->delete();

        Session::flash('flash_message', 'The category '.$category->name.' has been deleted.');

        return Redirect::route('event::categories.create');
    }
}
