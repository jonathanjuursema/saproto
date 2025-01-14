@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $category->title }} Board Archive
@endsection

@section('container')
    <div class="row">
        <div class="col-12">
            <div class="card mb-3 mx-4">
                <div class="card-header bg-dark text-white">
                    <span class="m-0 float-left">
                        <i class="fas fa-archive text-white me-2"></i>
                        Archived {{ $category->title }}
                    </span>
                    <a
                        href="{{ route('feedback::index', ['category' => $category->url]) }}"
                        class="float-end ms-3 px-2 py-1 btn btn-info"
                    >
                        <i class="fas fa-eye text-white"></i>
                        <span class="d-none d-sm-inline">View Public</span>
                    </a>
                </div>

                <div class="card-body">
                    @if (count($data) > 0)
                        <div class="row">
                            @foreach ($data as $key => $entry)
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    @include(
                                        'feedbackboards.include.feedback',
                                        [
                                            'feedback' => $entry,
                                            'controls' => true,
                                        ]
                                    )
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted mt-3">
                            No archived {{ $category->title }}.
                        </p>
                    @endif
                </div>

                @if ($data->links() != '')
                    <div class="card-footer">
                        {{ $data->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
