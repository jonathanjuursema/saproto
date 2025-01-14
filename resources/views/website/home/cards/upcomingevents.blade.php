<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-calendar-alt fa-fw me-2"></i>
        Upcoming events
    </div>
    <div class="card-body">
        @if (count($events) > 0)
            @foreach ($events as $counter => $event)
                @if ($event->mayViewEvent(Auth::user()) && $event->isPublished())
                    @include('event.display_includes.event_block', ['event' => $event, 'lazyload' => $counter > 4])

                    @php
                        $week = date('W', $event->start);
                    @endphp
                @endif
            @endforeach
        @else
            <p class="card-text text-center mt-2 mb-4">
                We have no events coming up this month, sorry! 😟
            </p>
        @endif

        <a href="{{ route('event::index') }}" class="btn btn-info btn-block">
            Go to the calendar
        </a>
    </div>
</div>
