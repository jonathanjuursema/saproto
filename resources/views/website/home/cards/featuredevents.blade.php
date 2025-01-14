@if (count($featuredEvents) > 0)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-star fa-fw me-2"></i>
            Featured events
        </div>
        <div class="card-body">
            @foreach ($featuredEvents as $key => $event)
                @include('event.display_includes.event_block', ['event' => $event, 'countdown' => true])

                @php($week = date('W', $event->start))
            @endforeach
        </div>
    </div>
@endif
