<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class MenuController extends Controller
{
    /** @return View */
    public function index()
    {
        $menuItems = MenuItem::query()->whereNull('parent')->with('children', 'page')->orderBy('order')->get();

        return view('menu.list', ['menuItems' => $menuItems]);
    }

    /** @return View */
    public function create(Router $router)
    {
        $pages = Page::all();
        $topMenuItems = MenuItem::query()->where('parent')->orderBy('order')->get();

        return view('menu.edit', ['item' => null, 'pages' => $pages, 'topMenuItems' => $topMenuItems, 'routes' => $this->getAllRoutes($router)]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $menuItem = new MenuItem;
        $menuItem->menuname = $request->input('name');
        $menuItem->parent = $request->input('parent') ?: null;
        $menuItem->is_member_only = $request->has('is_member_only');
        $menuItem->page_id = $request->input('page_id') ?: null;
        $menuItem->url = $menuItem->page_id ? Page::query()->find($menuItem->page_id)->getUrl() : $request->input('url');
        $maxOrder = MenuItem::query()->where('parent', $menuItem->parent)->orderBy('order', 'DESC')->first();
        $menuItem->order = $maxOrder ? $maxOrder->order + 1 : 0;
        $menuItem->save();

        $this->fixDuplicateMenuItemsOrder($menuItem->parent);

        Cache::forget('website.navbar');

        return Redirect::route('menu::list');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit(Router $router, $id)
    {
        $menuItem = MenuItem::query()->findOrFail($id);
        $pages = Page::all();
        $topMenuItems = MenuItem::query()->where('parent', null)->orderBy('order')->get();

        return view('menu.edit', ['item' => $menuItem, 'pages' => $pages, 'topMenuItems' => $topMenuItems, 'routes' => $this->getAllRoutes($router)]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var MenuItem $menuItem */
        $menuItem = MenuItem::query()->findOrFail($id);

        if ($request->input('parent') != $menuItem->parent) {
            $oldparent = $menuItem->parent;
        }

        $menuItem->menuname = $request->input('name');
        $menuItem->parent = $request->input('parent') ?: null;
        $menuItem->is_member_only = $request->has('is_member_only');
        $menuItem->page_id = $request->input('page_id') ?: null;
        $menuItem->url = $menuItem->page_id ? Page::query()->find($menuItem->page_id)->getUrl() : $request->input('url');
        $maxOrder = MenuItem::query()->where('parent', $menuItem->parent)->orderBy('order', 'DESC')->first();
        $menuItem->order = $maxOrder ? $maxOrder->order + 1 : 0;
        $menuItem->save();

        if (isset($oldparent)) {
            $this->fixDuplicateMenuItemsOrder($oldparent);
        }

        $this->fixDuplicateMenuItemsOrder($menuItem->parent);
        Cache::forget('website.navbar');

        return Redirect::route('menu::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function orderUp($id)
    {
        /** @var MenuItem $menuItem */
        $menuItem = MenuItem::query()->findOrFail($id);
        $menuItemAbove = MenuItem::query()->where('parent', $menuItem->parent)->where('order', '<', $menuItem->order)->orderBy('order', 'desc')->first();

        if (! $menuItemAbove) {
            abort(400, 'Item is already top item.');
        }

        $this->switchMenuItems($menuItem, $menuItemAbove);
        $this->fixDuplicateMenuItemsOrder($menuItem->parent);
        Cache::forget('website.navbar');

        return Redirect::route('menu::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function orderDown($id)
    {
        /** @var MenuItem $menuItem */
        $menuItem = MenuItem::query()->findOrFail($id);
        $menuItemBelow = MenuItem::query()->where('parent', $menuItem->parent)->where('order', '>', $menuItem->order)->orderBy('order', 'asc')->first();

        if (! $menuItemBelow) {
            abort(400, 'Item is already bottom item.');
        }

        $this->switchMenuItems($menuItem, $menuItemBelow);
        $this->fixDuplicateMenuItemsOrder($menuItem->parent);
        Cache::forget('website.navbar');

        return Redirect::route('menu::list');
    }

    private function switchMenuItems(MenuItem $item1, MenuItem $item2): void
    {
        $newOrderForItem1 = $item2->order;
        $newOrderForItem2 = $item1->order;

        $item1->order = $newOrderForItem1;
        $item2->order = $newOrderForItem2;

        $item1->save();
        $item2->save();
    }

    private function fixDuplicateMenuItemsOrder(int $parent): void
    {
        $menuItems = MenuItem::query()->where('parent', $parent)->orderBy('order')->get();
        $i = 0;
        foreach ($menuItems as $menuItem) {
            $menuItem->order = $i;
            $menuItem->save();
            $i++;
        }
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(int $id)
    {
        /** @var MenuItem $menuItem */
        $menuItem = MenuItem::query()->findOrfail($id);

        if ($menuItem->children->count() > 0) {
            Session::flash('flash_message', "A menu item with children can't be removed.");

            return Redirect::route('menu::list');
        }

        $change = MenuItem::query()->where('parent', '=', $menuItem->parent)->get();

        foreach ($change as $item) {
            if ($item->order > $menuItem->order && $item->id != $menuItem->id) {
                $item->order--;
                $item->save();
            }
        }

        Session::flash('flash_message', 'Menu item has been removed.');
        $menuItem->delete();
        Cache::forget('website.navbar');

        return Redirect::route('menu::list');
    }

    private function getAllRoutes(Router $router): ?array
    {
        $routes = $router->getRoutes()->getRoutesByMethod()['GET'];

        return array_filter($routes, static fn ($route): bool => $route->getName() &&
            ! str_contains($route->uri(), '{') &&
            ! str_contains($route->getName(), 'api::') &&
            ! str_contains($route->getName(), 'login::') &&
            ! str_contains($route->uri(), 'oauth'));
    }
}
