@if(!$event->secret || (
Auth::check() && (($event->activity && $event->activity->isParticipating(Auth::user())) || Auth::user()->can('board'))
))

    <a class="card mb-3 leftborder leftborder-info" style="text-decoration: none !important;"
       href="{{ route('event::show', ['id' => $event->getPublicId()]) }}">

        <div class="card-body event {{ $event->image && (!isset($hide_photo) || !$hide_photo) ? sprintf('bg-img') : sprintf('no-img')}}" style="text-align: left;
                {{ $event->image && (!isset($hide_photo) || !$hide_photo) ? sprintf('background: url(%s);', $event->image->generateImagePath(800,300)) : '' }}">

            @if(isset($countdown) && $countdown)
                <div class="btn btn-info btn-block mb-3 ">
                    <i class="fas fa-history fa-fw fa-pulse mr-2" aria-hidden="true"></i>
                    <span class="proto-countdown" data-countdown-start="{{ $event->start }}" data-countdown-text-counting="Starts in {}" data-countdown-text-finished="Event is underway!">
                        Counting down...
                    </span>
                </div>
            @endif

            @if($event->secret)
                <span class="badge badge-info float-right"
                      data-toggle="tooltip" data-placement="top" title="Secret activities can only be visited if you know the link. You can see it now because you are an admin.">
                    <i class="fas fa-eye-slash fa-fw mr-1"></i> Secret activity!</span>
            @endif

            @if($event->is_featured)
                <i class="fas fa-star fa-fw" aria-hidden="true" style="color: gold"
                   data-toggle="tooltip" data-placement="top" title="This is a featured activity!"></i>
            @endif
            @if($event->activity && Auth::check())
                @if($event->activity->isParticipating(Auth::user()))
                    <i class="fas fa-check text-primary fa-fw" aria-hidden="true"
                       data-toggle="tooltip" data-placement="top" title="You are participating!"></i>
                @endif
                @if($event->activity->isHelping(Auth::user()))
                    <i class="fas fa-life-ring fa-fw" style="color: red;" aria-hidden="true"
                       data-toggle="tooltip" data-placement="top" title="You are helping!"></i>
                @endif
            @endif
            @if (Auth::check() && $event->hasBoughtTickets(Auth::user()))
                <i class="fas fa-ticket-alt fa-fw" style="color: dodgerblue;" aria-hidden="true"
                   data-toggle="tooltip" data-placement="top" title="You bought a ticket!"></i>
            @endif
            @if (Auth::check() && Auth::user()->member && $event->activity && $event->activity->inNeedOfHelp(Auth::user()))
                <i class="fas fa-exclamation-triangle fa-fw" style="color: red;" aria-hidden="true"
                   data-toggle="tooltip" data-placement="top" title="This activity needs your help!"></i>
            @endif
            <strong>{{ $event->title }}</strong>

            @if($event->is_educational)
                <br>

                <span>
                    <i class="fas fa-book-open fa-fw" aria-hidden="true"></i>
                    Study related
                </span>
            @endif

            <br>

                <span>
                    <i class="fas fa-clock fa-fw" aria-hidden="true"></i>
            @if (isset($include_year))
                        {{ $event->generateTimespanText('D j M Y, H:i', 'H:i', '-') }}
                    @else
                        {{ $event->generateTimespanText('D j M, H:i', 'H:i', '-') }}
                    @endif
                </span>

            <br>

            <span>
                <i class="fas fa-map-marker-alt fa-fw" aria-hidden="true"></i>
                {{ $event->location }}
            </span>

            @if($event->is_external)
                <br>

                <span>
                    <i class="fas fa-info-circle fa-fw" aria-hidden="true"></i>
                    Not Organized by S.A. Proto
                </span>
            @endif

        </div>

    </a>

@endif