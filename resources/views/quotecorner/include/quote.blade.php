<div class="card h-100">

    <div class="card-header bg-dark text-white">

        <span class="qq_like mr-3" data-id="{{ $quote->id }}" style="cursor: pointer;">
            <i class="fa-thumbs-up {{ $quote->liked(Auth::user()->id) ? "fas" : "far" }}"></i>
            <span>{{ count($quote->likes()) }}</span>
        </span>

        Posted by
        @if($quote->user->isMember)
            <a href="{{ route('user::profile', ['id' => $quote->user->getPublicId()]) }}" class="text-white">
                {{ $quote->user->name }}
            </a>
        @else
            {{ $quote->user->name }}
        @endif

        @if (Auth::check() && Auth::user()->can("board"))
            <a href="{{ route('quotes::delete', ['id' => $quote->id]) }}" class="float-right ml-3"><i
                        class="fas fa-trash-alt text-white"></i></a>
        @endif

    </div>

    <div class="card-body">

        <i class="text-muted fas fa-quote-left fa-pull-left"></i>

        {!! $quote["quote"] !!}


        <i class="text-muted fas fa-quote-right fa-pull-right"></i>

        <div class="text-muted text-right mt-2">
            <em><sub>-- {{ $quote->created_at->format("j M Y, H:i") }}</sub></em>
        </div>

    </div>

</div>
