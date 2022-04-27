<div class="goodidea card mb-3 h-100">

    <div class="card-header bg-dark text-white">

        <span data-id="{{ $idea->id }}">
            <span class="votes d-inline-block">{{ $idea->voteScore() }}</span>
            <i class="upvote fa-thumbs-up {{ $idea->userVote(Auth::user()) == 1 ? "fas" : "far" }}"></i>
            <i class="downvote fa-thumbs-down {{ $idea->userVote(Auth::user()) == -1 ? "fas" : "far" }}"></i>
        </span>

        @if (Auth::user()->can("board") || Auth::user()->id == $idea->user->id)
            <a href="{{ route('goodideas::delete', ['id' => $idea->id]) }}" class="float-end ms-3"><i
                        class="fas fa-trash-alt text-white"></i></a>
        @endif

    </div>

    <div class="card-body">

        {!! $idea->idea !!}

    </div>

    <div class="card-footer ps-0">

        <div class="text-muted text-end mt-2">
            <em>
                <sub>
                    @can('board')
                        By {{ $idea->user->name }}
                    @endcan
                     -- {{ $idea->created_at->format("j M Y, H:i") }}
                </sub>
            </em>
        </div>
    </div>

</div>
