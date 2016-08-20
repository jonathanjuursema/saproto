@extends('website.layouts.default')

@section('page-title')
    Menu Admin
@endsection

@section('content')


                    @if (count($menuItems) > 0)

                        <table class="table">

                            <thead>

                            <tr>

                                <th>Menuname</th>
                                <th>URL</th>
                                <th>Visibility</th>
                                <th>Controls</th>

                            </tr>

                            </thead>

                        @foreach($menuItems as $menuItem)

                            <tr>
                                <td>{{ $menuItem->menuname }}</td>
                                <td>@if($menuItem->page) {{ $menuItem->page->getUrl() }} @else {{ $menuItem->url }} @endif</td>
                                <td>@if($menuItem->is_member_only) Member only @else Public @endif</td>
                                <td>
                                    <a class="btn btn-xs btn-default"
                                        href="{{ route('menu::edit', ['id' => $menuItem->id]) }}" role="button">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </a>

                                    <a class="btn btn-xs btn-danger"
                                       href="{{ route('menu::delete', ['id' => $menuItem->id]) }}" role="button">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </a>

                                    @if($menuItem->order > 0)
                                        <a class="btn btn-xs btn-default"
                                       href="{{ route('menu::orderUp', ['id' => $menuItem->id]) }}" role="button">
                                            <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                    @if($menuItem->order != $menuItems->count() - 1)
                                        <a class="btn btn-xs btn-default"
                                           href="{{ route('menu::orderDown', ['id' => $menuItem->id]) }}" role="button">
                                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if($menuItem->children->count() > 0)

                                @foreach($menuItem->children()->orderBy('order')->get() as $childItem)
                                        <tr>
                                            <td>&mdash;{{ $childItem->menuname }}</td>
                                            <td>@if($childItem->page) {{ $childItem->page->getUrl() }} @else {{ $childItem->url }} @endif</td>
                                            <td>@if($childItem->is_member_only) Member only @else Public @endif</td>
                                            <td>
                                                <a class="btn btn-xs btn-default"
                                                   href="{{ route('menu::edit', ['id' => $childItem->id]) }}" role="button">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>

                                                <a class="btn btn-xs btn-danger"
                                                   href="{{ route('menu::delete', ['id' => $childItem->id]) }}" role="button">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>

                                                @if($childItem->order > 0)
                                                    <a class="btn btn-xs btn-default"
                                                       href="{{ route('menu::orderUp', ['id' => $childItem->id]) }}" role="button">
                                                        <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                                    </a>
                                                @endif

                                                @if($childItem->order != $menuItem->children()->orderBy('order')->get()->count() - 1)
                                                    <a class="btn btn-xs btn-default"
                                                       href="{{ route('menu::orderDown', ['id' => $childItem->id]) }}" role="button">
                                                        <i class="fa fa-arrow-down" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                @endforeach

                            @endif

                        @endforeach

                            </table>

                            <p style="text-align: center;"><a href="{{ route('menu::add') }}">Create a new menu item.</a></p>

                    @else

                        <p style="text-align: center;">There are no menu items. <a href="{{ route('menu::add') }}">Create a new menu item.</a></p>

                    @endif


@endsection