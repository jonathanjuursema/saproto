@php
    /** @var int $n */
    $featuredevents = App\Models\Event::where('secret', false)->where('is_featured', true)->where('end', '>=', date('U'))->orderBy('start')->limit($n)->get()
@endphp

@if(count($featuredevents) > 0)

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-star fa-fw me-2"></i> Featured events
        </div>
        <div class="card-body">


            @foreach($featuredevents as $key => $event)

                @include('event.display_includes.event_block', ['event'=> $event, 'countdown' => true])

                @php($week = date('W', $event->start))

            @endforeach

        </div>
    </div>

@endif
