@extends('website.layouts.redesign.generic')

@section('page-title')
    Event Category Admin
@endsection

@section('container')

    <div class="row">
        
        <div class="col-5">
            <div class="card">
                <div class="card-header">
                    {{ $cur_category == null ? 'Add new category' : 'Edit category: '.$cur_category->name }}
                </div>
                <div class="card-body">
                    <form method="post"
                          action="{{ ($cur_category == null ? route('event::category::add') : route('event::category::edit', ['id' => $cur_category])) }}"
                          enctype="multipart/form-data">
                        {!! csrf_field() !!}

                        <label for="name">Category Name:</label>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="OmNomNom" value="{{ $cur_category->name ?? '' }}" required>

                        <div class="form-group mt-2">
                            <label class="mb-0">Category Icon:</label>
                            <label data-placement="inline" class="icp icp-auto"
                                   data-selected="{{ $cur_category ? $cur_category->icon : null }}"></label>
                            <input type="hidden" name="icon" id="icon" value="{{ $cur_category ? $cur_category->icon : null }}">
                        </div>

                        <button type="submit" class="btn btn-success float-right">Submit</button>
                        @if($cur_category)
                            <a class="btn btn-warning float-right mr-1" href="{{ route('event::category::admin') }}">Cancel</a>
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
                    @php($categories = \Proto\Models\EventCategory::all())
                    @if(count($categories) > 0)
                        @foreach($categories as $category)
                            <div class="col-5 row m-1">
                                <div class="px-4 py-2 my-2 w-75 rounded-left overflow-hidden ellipsis {{ $category == $cur_category ? 'bg-warning' : 'bg-info' }}">
                                    <i class="{{ $category->icon }} mr-2"></i>
                                    {{ $category->name }}
                                </div>
                                <div class="bg-white px-2 py-2 my-2 w-25 rounded-right">
                                    <a href="{{ route('event::category::admin', ['id' => $category]) }}">
                                        <i class="fas fa-edit mr-2 ml-1 mt-1"></i>
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#delete-category-modal" data-category-id="{{ $category->id }}">
                                        <i class="fas fa-trash mt-1 text-danger"></i>
                                    </a>
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

    <div class="modal fade" id="delete-category-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog model-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category. All events that currently have this category will become <b>uncategorised</b>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a id="delete-category" class="btn btn-danger" href="#">Delete Category</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        $('.icp-auto').iconpicker();
        $('.icp').on('iconpickerSelected', function (e) {
            $('#icon').val(e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue));
        });

        $('#delete-category-modal').on('show.bs.modal', function(e) {
            let categoryId = $(e.relatedTarget).data('category-id');
            $('#delete-category').attr('href', "{{ route('event::category::delete', ['id' => ':id']) }}".replace(':id', categoryId));
        });
    </script>
@endpush