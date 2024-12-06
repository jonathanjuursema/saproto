@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($list == null ? "Create a new list." : "Edit list " . $list->name .".") }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-4">

            <form method="post"
                  action="{{ ($list == null ? route("email::list::store") : route("email::list::update", ['id' => $list->id])) }}"
                  enctype="multipart/form-data">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">List name:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Members will see this name, make it descriptive."
                                   value="{{ $list->name ?? '' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="editor">Description</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'description',
                                'placeholder' => 'Text goes here.',
                                'value' => $list ? $list->description: null
                            ])
                        </div>

                        @include('components.forms.checkbox', [
                            'name' => 'is_member_only',
                            'checked' => $list?->is_member_only ,
                            'label' => 'Only for members'
                        ])

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end">Submit</button>

                        <a href="{{ route("email::index") }}" class="btn btn-default">Cancel</a>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
