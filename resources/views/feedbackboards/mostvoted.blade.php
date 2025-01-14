@if ($mostVoted)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Most upvoted recent
            {{ strtolower(str_singular($category->title)) }}
        </div>
        <div class="card-body">
            @include(
                'feedbackboards.include.feedback',
                [
                    'feedback' => $mostVoted,
                    'controls' => false,
                ]
            )
        </div>
    </div>
@endif
