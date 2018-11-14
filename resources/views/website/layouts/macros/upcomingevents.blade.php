<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-calendar-alt fa-fw mr-2"></i> Upcoming events
    </div>
    <div class="card-body">

        @php($events = Event::where('secret', false)->where('end', '>=', date('U'))->orderBy('start')->limit($n)->get())

        @if(count($events) > 0)

            @foreach($events as $key => $event)

                @include('event.display_includes.event_block', ['event'=> $event])

                <?php $week = date('W', $event->start); ?>

            @endforeach

        @else

            <p class="card-text text-center mt-2 mb-4">
                We have no events coming up soon, sorry! 😟
            </p>

        @endif

        <a href="{{ route("event::list") }}" class="btn btn-info btn-block">Go to the calendar</a>

    </div>
</div>