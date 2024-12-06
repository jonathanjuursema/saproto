@extends('website.layouts.redesign.generic')

@section('page-title')
    Event Category Admin
@endsection

@section('container')

    <div class="row">

        <div class="col-5">
            <div class="card">
                <div class="card-header">
                    {{ $cur_category == null ? 'Add new category' : 'Edit category: ' . $cur_category->name }}
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="{{ ($cur_category == null ? route('feedback::category::store') : route('feedback::category::update', ['id' => $cur_category])) }}"
                          enctype="multipart/form-data">
                        {!! csrf_field() !!}

                        <label for="name">Category Name:</label>
                        <input type="text" class="form-control mb-3" id="name" name="name"
                               placeholder="Exciting feedback!" value="{{ $cur_category->title ?? '' }}" required>

                        @include('components.forms.checkbox', [
                                    'name' => 'can_review',
                                    'checked' => $cur_category->review??false,
                                    'label' => 'Review feedback from the category before publishing?'
                                ])

                        <div id="reviewer" class="{{$cur_category?->review?"":"d-none"}}">
                            <label for="user_id">Reviewer Name:</label>
                            <div class="form-group autocomplete">
                                <input class="form-control user-search" value="{{ $cur_category->reviewer_id ?? '' }}"
                                       id="user_id" name="user_id" />
                            </div>
                        </div>

                        @include('components.forms.checkbox', [
                                   'name' => 'can_reply',
                                   'checked' => $cur_category->can_reply ?? true,
                                   'label' => 'Should the board be able to reply to this feedback?'
                               ])

                        @include('components.forms.checkbox', [
                                  'name' => 'show_publisher',
                                  'checked' => $cur_category->show_publisher ?? false,
                                  'label' => 'Show the name of the person who submits the feedback?'
                              ])

                        <button type="submit" class="btn btn-success float-end"> Submit</button>
                        @if($cur_category)
                            <a class="btn btn-warning float-end me-1" href="{{ route('feedback::category::admin') }}">Cancel</a>
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
                        @if(count($categories) > 0)
                            @foreach($categories as $category)
                                <div class="col-5 row m-1 w-100">
                                    <div
                                        class="px-4 py-2 my-2 w-75 rounded-start overflow-hidden ellipsis {{ $category == $cur_category ? 'bg-warning' : 'bg-info' }}">
                                        <a href="{{ route('feedback::index', ['category' => $category->url]) }}"
                                           class="text-reset">{{ $category->title }}</a> {{ $category->reviewer ? " | Reviewer:" . $category->reviewer->calling_name : "" }}
                                    </div>
                                    <div class="bg-white px-2 py-2 my-2 w-25 rounded-end">
                                        <a href="{{ route('feedback::category::admin', ['id' => $category]) }}">
                                            <i class="fas fa-edit me-2 ms-1 mt-1 float-end"></i>
                                        </a>
                                        @include('components.modals.confirm-modal', [
                                            'action' => route('feedback::category::delete', ['id' => $category->id]),
                                            'classes' => 'fas fa-trash mt-1 text-danger float-end',
                                            'confirm'=>'Delete',
                                            'text'=>'',
                                            'title' => 'Confirm Delete',
                                            'message' => "Are you sure you want to delete <b><i>$category->title</i></b>. All feedback that currently has this category will become <b>uncategorised!</b>",
                                        ])
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted mb-0">
                                There are no feedback categories yet.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        let checkbox = document.getElementById('can_review');
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                document.getElementById('reviewer').classList.remove('d-none');
                document.getElementById('user_id').required = true;
            } else {
                document.getElementById('reviewer').classList.add('d-none');
                document.getElementById('user_id').required = false;
            }
        });
    </script>
@endpush
