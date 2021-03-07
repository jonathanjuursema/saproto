<div class="card mb-3 h-100">

    <div class="card-header bg-dark text-white">


        <span>
            <span data-id="{{ $idea->id }}">{{ $idea->voteScore() }}</span>
            <a data-id="{{ $idea->id }}"
               class="gi_upvote fa-thumbs-up {{ $idea->userVote(Auth::user()) == 1 ? "fas" : "far" }}"></a>
            <a data-id="{{ $idea->id }}"
               class="gi_downvote fa-thumbs-down {{ $idea->userVote(Auth::user()) == -1 ? "fas" : "far" }}"></a>
        </span>

        @if (Auth::user()->can("board") || Auth::user()->id == $idea->user->id)
            <a href="{{ route('goodideas::delete', ['id' => $idea->id]) }}" class="float-right ml-3"><i
                        class="fas fa-trash-alt text-white"></i></a>
        @endif

    </div>

    <div class="card-body">

        {!! $idea->idea !!}


    </div>

    <div class="card-footer pl-0">

        <div class="text-muted text-right mt-2">
            <em>
                <sub>
                    @if(Auth::user()->can('board'))
                        By {{ $idea->user->name }}
                    @endif
                     -- {{ $idea->created_at->format("j M Y, H:i") }}
                </sub>
            </em>
        </div>
    </div>

</div>
