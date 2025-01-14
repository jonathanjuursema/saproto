@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ isset($item) ? 'Edit menu item' : 'Add new menu item' }}
@endsection

@section('container')
    <form
        method="post"
        action="{{ ! isset($item) ? route('menu::create') : route('menu::update', ['id' => $item->id]) }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="About Proto"
                                value="{{ $item->menuname ?? '' }}"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label for="parent">Parent:</label>
                            <select
                                class="form-control"
                                name="parent"
                                id="parent"
                            >
                                <option
                                    @selected(! isset($item) || $item->parent == null)
                                    value
                                >
                                    No parent
                                </option>
                                @foreach ($topMenuItems as $topMenuItem)
                                    <option
                                        value="{{ $topMenuItem->id }}"
                                        @selected(isset($item) && $topMenuItem->id == $item->parent)
                                        @disabled(isset($item) && $topMenuItem->id == $item->id)
                                    >
                                        {{ $topMenuItem->menuname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @include(
                            'components.forms.checkbox',
                            [
                                'name' => 'is_member_only',
                                'checked' => $item?->is_member_only,
                                'label' =>
                                    '<i class="fas fa-lock" aria-hidden="true"></i> Members only',
                            ]
                        )

                        <div class="form-group">
                            <label for="page_id">Target page:</label>
                            <select
                                class="form-control"
                                name="page_id"
                                id="page_id"
                            >
                                <option disabled @selected(! isset($item))>
                                    Select a page...
                                </option>
                                <option disabled>---</option>
                                <option
                                    id="other-url-option"
                                    @selected(isset($item) && $item->page_id)
                                    value
                                >
                                    Other URL
                                </option>
                                <option disabled>---</option>
                                @foreach ($pages as $page)
                                    <option
                                        @selected(isset($item) && $page->id == $item->page_id)
                                        value="{{ $page->id }}"
                                    >
                                        {{ $page->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div
                            id="other-url-fields"
                            class="{{ ! isset($item) || isset($item->page_id) ? 'd-none' : '' }}"
                        >
                            <div class="form-group">
                                <label for="url">Other URL:</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="url"
                                    name="url"
                                    placeholder="http://www.proto.utwente.nl/"
                                    value="{{ $item->url ?? '' }}"
                                />
                            </div>

                            <div class="form-group">
                                <label for="route">Existing Route:</label>
                                <select class="form-control" id="route">
                                    <option
                                        disabled
                                        @selected(! isset($item) || $item->url == null)
                                    >
                                        Select a route...
                                    </option>
                                    @foreach ($routes as $route)
                                        @php
                                            $domain = $route->domain() == null ? Config::string('app-proto.primary-domain') : $route->domain();
                                            $uri = $route->uri() == '/' ? '' : $route->uri();
                                            $url = "https://$domain/$uri";
                                        @endphp

                                        <option
                                            value="{{ $url }}"
                                            @selected(isset($item) && $item->url == '(route) ' . $route->getName())
                                        >
                                            [{{ $route->getName() }}] ->
                                            {{ $route->domain() }}/{{ $route->uri }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('menu::list') }}"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const otherUrlOption = document.getElementById('other-url-option')
        const otherUrlFields = document.getElementById('other-url-fields')
        document.getElementById('page_id').addEventListener('change', (e) => {
            if (otherUrlOption.selected)
                otherUrlFields.classList.remove('d-none')
            else otherUrlFields.classList.add('d-none')
        })

        document.getElementById('route').addEventListener('change', (e) => {
            document.getElementById('url').value = e.target.value
        })
    </script>
@endpush
