@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Menu Admin
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a
                        href="{{ route('menu::create') }}"
                        class="badge bg-info float-end"
                    >
                        Create a new menu item.
                    </a>
                </div>

                @if (count($menuItems) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr class="bg-dark text-white">
                                    <td>Menuname</td>
                                    <td>URL</td>
                                    <td>Visibility</td>
                                    <td>Controls</td>
                                </tr>
                            </thead>
                            @foreach ($menuItems as $index => $menuItem)
                                <tr class="bg-info text-white">
                                    <td>
                                        <i class="fas fa-folder me-2"></i>
                                        {{ $menuItem->menuname }}
                                    </td>
                                    <td>{{ $menuItem->getUrl() }}</td>
                                    <td>
                                        @if ($menuItem->is_member_only)
                                            <i class="fas fa-lock"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('menu::edit', ['id' => $menuItem->id]) }}"
                                        >
                                            <i
                                                class="fas fa-edit me-2 text-white"
                                            ></i>
                                        </a>

                                        @include(
                                            'components.modals.confirm-modal',
                                            [
                                                'action' => route('menu::delete', ['id' => $menuItem->id]),
                                                'text' => '<i class="fas fa-trash me-2 text-danger"></i>',
                                                'title' => 'Confirm Delete',
                                                'message' => 'Are you sure you want to delete this menu item?',
                                                'confirm' => 'Delete',
                                            ]
                                        )

                                        @if (! $index == 0)
                                            <a
                                                href="{{ route('menu::orderUp', ['id' => $menuItem->id]) }}"
                                            >
                                                <i
                                                    class="fas fa-arrow-up me-2 text-white"
                                                ></i>
                                            </a>
                                        @endif

                                        @if (! $index == count($menuItems))
                                            <a
                                                href="{{ route('menu::orderDown', ['id' => $menuItem->id]) }}"
                                            >
                                                <i
                                                    class="fas fa-arrow-down me-2 text-white"
                                                ></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @if ($menuItem->children->count() > 0)
                                    @foreach ($menuItem->children()->orderBy('order')->get()as $childItem)
                                        <tr>
                                            <td class="ps-5">
                                                {{ $childItem->menuname }}
                                            </td>
                                            <td>
                                                @if ($childItem->page)
                                                    {{ $childItem->page->getUrl() }}
                                                @else
                                                    {{ $childItem->url }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($childItem->is_member_only)
                                                    <i class="fas fa-lock"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <a
                                                    href="{{ route('menu::edit', ['id' => $childItem->id]) }}"
                                                >
                                                    <i
                                                        class="fas fa-edit me-2"
                                                    ></i>
                                                </a>

                                                @include(
                                                    'components.modals.confirm-modal',
                                                    [
                                                        'action' => route('menu::delete', ['id' => $childItem->id]),
                                                        'text' => '<i class="fas fa-trash me-2 text-danger"></i>',
                                                        'title' => 'Confirm Delete',
                                                        'message' => 'Are you sure you want to delete this menu item?',
                                                        'confirm' => 'Delete',
                                                    ]
                                                )

                                                @if (! $childItem->isFirst())
                                                    <a
                                                        href="{{ route('menu::orderUp', ['id' => $childItem->id]) }}"
                                                    >
                                                        <i
                                                            class="fas fa-arrow-up me-2"
                                                        ></i>
                                                    </a>
                                                @endif

                                                @if (! $childItem->isLast())
                                                    <a
                                                        href="{{ route('menu::orderDown', ['id' => $childItem->id]) }}"
                                                    >
                                                        <i
                                                            class="fas fa-arrow-down me-2"
                                                        ></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </table>
                    </div>
                @else
                    <div class="card-body">
                        <p class="card-text">There are no menu items.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
