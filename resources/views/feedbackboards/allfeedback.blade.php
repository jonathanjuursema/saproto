<div class="card mb-3 w-100">
    <div class="card-header bg-dark text-white">
        <div
            class="d-flex align-items-center justify-items-center justify-content-between mb-2"
        >
            <span class="">{{ $category->title }}</span>
            @can('board')
                <div>
                    @if (count($data) > 0)
                        <a
                            href="{{ route('feedback::archiveall', ['category' => $category->url]) }}"
                            class="ms-3 btn btn-danger"
                        >
                            <i class="fas fa-box-archive text-white"></i>
                            Archive all
                        </a>
                    @endif

                    <a
                        href="{{ route('feedback::archived', ['category' => $category->url]) }}"
                        class="float-end ms-3 btn btn-info"
                    >
                        <i class="fas fa-inbox text-white"></i>
                        <span class="d-none d-sm-inline">View archived</span>
                    </a>
                </div>
            @endcan
        </div>
        @if ($data->links() != '')
            {{ $data->withQueryString()->links() }}
        @endif
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
                There are no {{ $category->title }} at the moment, be the first
                to post a new one!
            </p>
        @endif
    </div>

    @if ($data->links() != '')
        <div class="card-footer">
            {{ $data->withQueryString()->links() }}
        </div>
    @endif
</div>
