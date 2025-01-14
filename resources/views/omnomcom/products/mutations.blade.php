@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Stock Mutations
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">Filters</div>
                <div class="card-body">
                    <form
                        id="mut_filter_form"
                        method="get"
                        action="{{ route('omnomcom::products::mutations') }}"
                    >
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="product_name">Product name</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="product_name"
                                        name="product_name"
                                        placeholder="Will to live"
                                        value="{{ request()->input('product_name') }}"
                                    />
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="author_name">Author name</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="author_name"
                                        name="author_name"
                                        placeholder="John Doe"
                                        value="{{ request()->input('author_name') }}"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                @include(
                                    'components.forms.datetimepicker',
                                    [
                                        'name' => 'after',
                                        'label' => 'Show after:',
                                        'placeholder' => request()->has('after')
                                            ? strtotime(request()->input('after'))
                                            : 563886000,
                                    ]
                                )
                            </div>

                            <div class="col-md-12 mb-3">
                                @include(
                                    'components.forms.datetimepicker',
                                    [
                                        'name' => 'before',
                                        'label' => 'Show before:',
                                        'placeholder' => request()->has('before')
                                            ? strtotime(request()->input('before'))
                                            : time(),
                                    ]
                                )
                            </div>
                        </div>

                        <div class="row-md-12 mb-3">
                            @include(
                                'components.forms.checkbox',
                                [
                                    'name' => 'also_positive',
                                    'checked' => request()->has('also_positive'),
                                    'label' => 'Also show positive mutations',
                                ]
                            )
                        </div>
                        <div>
                            <button class="btn btn-success mb-3" type="submit">
                                Apply
                            </button>

                            <button
                                type="submit"
                                formaction="{{ route('omnomcom::products::mutations_export') }}"
                                class="btn btn-success mb-3"
                            >
                                Export as CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>
                <div class="card-body">
                    @if (count($mutations) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="bg-dark text-white">
                                        <td>Product</td>
                                        <td>Before</td>
                                        <td>After</td>
                                        <td>Delta</td>
                                        <td>User</td>
                                        <td>Made using bulk update</td>
                                        <td>Creation time</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mutations as $mutation)
                                        <tr>
                                            <td>
                                                {{ $mutation->product->name }}
                                            </td>
                                            <td>{{ $mutation->before }}</td>
                                            <td>{{ $mutation->after }}</td>
                                            <td
                                                class="text-{{ $mutation->delta() > 0 ? 'white' : 'danger' }}"
                                            >
                                                {{ $mutation->delta() }}
                                            </td>
                                            <td>
                                                <a
                                                    href="{{ route('user::profile', ['id' => $mutation->user->getPublicId()]) }}"
                                                >
                                                    {{ $mutation->user->name }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $mutation->is_bulk ? 'Yes' : 'No' }}
                                            </td>
                                            <td>{{ $mutation->date() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if (method_exists($mutations, 'links'))
                            <div class="card-footer pb-0">
                                {!! $mutations->links() !!}
                            </div>
                        @endif
                    @else
                        <h4>No mutations found!</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
