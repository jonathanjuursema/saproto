@if(count($committee->upcomingEvents()) > 0)

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Upcoming events
        </div>

        <div class="card-body">

            <div class="row">

                @foreach($committee->upcomingEvents() as $key => $event)
                    <div class="col-6">
                        @include('event.display_includes.event_block', [
                         'event' => $event
                        ])
                    </div>
                @endforeach

            </div>

        </div>

    </div>

@endif

@if(count($committee->pastEvents()) > 0)

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Recently organised
        </div>

        <div class="card-body">

            <div class="row">

                @foreach($committee->pastEvents()->slice(0, 6) as $key => $event)

                    <div class="col-6">
                        @include('event.display_includes.event_block', [
                            'event' => $event,
                            'include_year' => true
                        ])
                    </div>

                @endforeach

            </div>

        </div>

    </div>

@endif

@if(count($committee->helpedEvents()) > 0)

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Events recently helped at
        </div>

        <div class="card-body">

            <div class="row">

                @foreach(array_slice($committee->helpedEvents(), 0, 6) as $key => $event)
                    <div class="col-6">
                        @include('event.display_includes.event_block', [
                            'event' => $event,
                            'include_year' => true
                        ])
                    </div>
                @endforeach

            </div>

        </div>

    </div>

@endif
