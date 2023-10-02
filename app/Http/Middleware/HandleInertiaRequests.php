<?php

namespace App\Http\Middleware;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'inertia.app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $menuItems = MenuItem::where('parent', null)->orderBy('order')->with('children')->get();
        $permissions = Auth::user()?->getAllPermissions()->pluck('name')->mapWithKeys(function ($permission) {
            return [$permission => true];
        });


        return array_merge(parent::share($request), [
            'csrf' => csrf_token(),
            'menuItems' => $menuItems,
            'themes' => config('proto.themes'),
            'auth.user' => Auth::user(),
            'auth.permissions' => $permissions,
        ]);
    }
}
