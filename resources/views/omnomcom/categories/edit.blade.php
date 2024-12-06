@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($category == null ? "Create new category." : "Edit category " . $category->name .".") }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <form method="post"
                  action="{{ ($category == null ? route("omnomcom::categories::store") : route("omnomcom::categories::update", ['id' => $category->id])) }}"
                  enctype="multipart/form-data">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <label for="name">Category name:</label>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="Special Products for Unicorns" value="{{ $category->name ?? '' }}" required>
                    </div>

                    <div class="card-footer">

                        @if($category)
                            @include('components.modals.confirm-modal', [
                                'action' => route('omnomcom::categories::delete', ['id' => $category->id]),
                                'classes' => 'btn btn-danger',
                                'text' => 'Delete',
                                'title' => 'Confirm Delete',
                                'message' => "Are you sure you want to delete category $category->name",
                            ])
                        @endif

                        <button type="submit" class="btn btn-success float-end ms-3">Submit</button>

                        <a href="{{ route("omnomcom::categories::index") }}"
                           class="btn btn-default float-end">Cancel</a>

                    </div>

                </div>
                @if($category)
                    <div class="card mb-3">

                        <div class="card-header bg-dark text-white">
                            Products in this category
                        </div>

                        <ul class="list-group list-group-flush">
                            @foreach($category->sortedProducts() as $product)
                                <li class="list-group-item">
                                    {{ $product->name }}
                                    <a href="{{ route('omnomcom::products::edit', ['id' => $product->id]) }}">
                                        <i class="fa fa-edit float-end"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                @endif

            </form>

        </div>

    </div>

@endsection
