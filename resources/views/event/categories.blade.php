@extends('website.layouts.redesign.generic')

@section('page-title')
    Event Category Admin
@endsection

@section('container')

    <div class="row">

        <div class="col-5">
            <div class="card">
                <div class="card-header">
                    {{ empty($cur_category) ? 'Add new category' : 'Edit category: '.$cur_category->name }}
                </div>
                <div class="card-body">

                    <form
                        action="{{ !empty($cur_category) ? route('event::categories.update', ['category'=>$cur_category]) : route("event::categories.store") }}"

                        method="POST">
                        <input type="hidden" name="_method" value="{{ !empty($cur_category) ? "PUT" : "POST" }}">
                        @csrf

                        <label for="name">Category Name:</label>
                        <input type="text" class="form-control mb-3" id="name" name="name"
                               placeholder="OmNomNom" value="{{ $cur_category->name ?? '' }}" required>

                        @include('components.forms.iconpicker', [
                            'name' => 'icon',
                            'placeholder' => isset($cur_category) ? $cur_category->icon : null,
                            'label' => 'Category icon:'
                        ])

                        <button type="submit" class="btn btn-success float-end">Submit</button>
                        @if($cur_category)
                            <a class="btn btn-warning float-end me-1"
                               href="{{ route('event::categories.create') }}">Cancel</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-5">
            <div class="card">
                <div class="card-header">
                    Categories
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        @php($categories = \App\Models\EventCategory::all())
                        @if(count($categories) > 0)
                            @foreach($categories as $category)
                                <div class="col-5 row m-1">
                                    <div
                                        class="px-4 py-2 my-2 w-75 rounded-start overflow-hidden ellipsis {{ $category == $cur_category ? 'bg-warning' : 'bg-info' }}">
                                        <i class="{{ $category->icon }} me-2"></i>
                                        {{ $category->name }}
                                    </div>
                                    <div class="bg-white px-2 py-2 my-2 w-25 rounded-end">
                                        <a href="{{ route('event::categories.edit', ['category' => $category]) }}">
                                            <i class="fas fa-edit me-2 ms-1 mt-1"></i>
                                        </a>

                                        @include('components.modals.confirm-modal', [
                                          'action' => route('event::categories.destroy', ['category' => $category]),
                                          'method'=>'DELETE',
                                          'confirm' => 'Delete the event category',
                                          'classes' => 'fa fa-trash text-danger',
                                          'text' => '',
                                          'message' => "Are you sure you want to delete this category. All events that currently have this category will
                        become <b>uncategorised</b>",
                                      ])
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted mb-0">
                                There are no event categories yet.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
