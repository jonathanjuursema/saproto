@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($category == null ? "Create new category." : "Edit category " . $category->name .".") }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <form method="post"
                  action="{{ ($category == null ? route("omnomcom::categories::add") : route("omnomcom::categories::edit", ['id' => $category->id])) }}"
                  enctype="multipart/form-data">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <label for="name">Category name:</label>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="Special Products for Unicorns" value="{{ $category->name or '' }}" required>
                    </div>

                    <div class="card-footer">

                        @if($category)
                            <a class="btn btn-danger"
                               onclick="return confirm('Remove category \'{{ $category->name }}\'?');"
                               href="{{ route('omnomcom::categories::delete', ['id' => $category->id]) }}">
                                Delete
                            </a>
                        @endif

                        <button type="submit" class="btn btn-success float-right ml-3">Submit</button>

                        <a href="{{ route("omnomcom::categories::list") }}"
                           class="btn btn-default float-right">Cancel</a>

                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-3">

            @include('omnomcom.categories.edit_includes.order')

        </div>

    </div>

@endsection