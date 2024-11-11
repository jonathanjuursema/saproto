<div class="col-lg-3 col-md-6 col-sm-12 mb-3">

    <div class="card h-100">

        <div class="card-body">

            <h5 class="card-title text-center">{{ $month_name }}</h5>

            @if(count($events) > 0)

                @foreach($events??[] as $counter => $event)
                    @include('event.display_includes.event_block', [
                        'event'=> $event,
                        'lazyload' => $counter > 5
                    ])
                @endforeach

            @else

                <div class="card-text text-muted text-center">
                    No activities {{strtolower($month_name)}} 😟
                </div>

            @endif

        </div>

    </div>

</div>
