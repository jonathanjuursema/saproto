@if (count($unreviewed))
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            {{ $category->title }} (to be reviewed)
        </div>

        <div class="card-body">
            <div class="row">
                @foreach ($unreviewed as $feedback)
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        @include(
                            'feedbackboards.include.feedback',
                            [
                                'feedback' => $feedback,
                                'controls' => false,
                            ]
                        )
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
