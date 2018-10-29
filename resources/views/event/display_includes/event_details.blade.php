@if ($event->secret)
    <div class="alert alert-info" role="alert">
        This event is not shown on the site, you can only access it directly via the URL.
    </div>
@endif

@if(Auth::check() && ($event->isEventAdmin(Auth::user()) || Auth::user()->can('board')))

    <div class="row align-content-center mb-3" role="group">

        @if($event->isEventAdmin(Auth::user()))
            <div class="col-6">
                <a href="{{ route("event::admin", ['id'=>$event->id]) }}" class="btn btn-primary btn-block">
                    Admin
                </a>
            </div>
        @endif

        @if(Auth::user()->can('board'))
            <div class="col-6">
                <a href="{{ route("event::edit", ['id'=>$event->id]) }}" class="btn btn-info btn-block">
                    Edit
                </a>
            </div>
        @endif

    </div>

@endif

<div class="card mb-3">

    @if($event->image)
        <img class="card-img-top" src="{{ $event->image->generateImagePath(800, 300) }}" width="100%">
    @endif

    <div class="card-body">

        <h5 class="card-title">@yield('page-title')</h5>

        @if($event->committee)
            <h6 class="card-subtitle mb-2 text-muted">
                This activity is organised by the
                <a href="{{ route('committee::show', ['id' => $event->committee->getPublicId()]) }}">{{ $event->committee->name }}</a>.
            </h6>
        @endif

    </div>

    <ul class="list-group list-group-flush">

        <li class="list-group-item">
            <i class="fas fa-fw fa-clock" aria-hidden="true"></i>
            {{ $event->generateTimespanText('l j F Y, H:i', 'H:i', 'till') }}
        </li>

        <li class="list-group-item">
            <i class="fas fa-fw fa-map-marker-alt" aria-hidden="true"></i> {{ $event->location }}
        </li>

        @if ($event->involves_food == true)
            <a class="list-group-item bg-info text-white" href="{{ route("user::dashboard") }}#alergies">
                <i class="fas fa-fw fa-utensils" aria-hidden="true"></i> There will be food, please indicate
                any allergies or diets on your dashboard
            </a>
        @endif

        @if ($event->is_educational == true)
            <li class="list-group-item">
                <i class="fas fa-fw fa-book-open" aria-hidden="true"></i> This event is directly related to your study
            </li>
        @endif

        @if ($event->is_external == true)
            <li class="list-group-item">
                <i class="fas fa-fw fa-info-circle" aria-hidden="true"></i> This event is not organized by S.A. Proto
            </li>
        @endif

    </ul>

    <div class="card-body">

        {!! Markdown::convertToHtml($event->description) !!}

    </div>

</div>

@if($event->videos->count() || $event->albums->count())

    <div class="card">

        <div class="card-header text-center bg-dark text-white">
            Media from this event
        </div>

        <div class="card-body">

            @if($event->videos->count() > 0)

                @foreach($event->videos as $video)

                    @include('website.layouts.macros.card-bg-image', [
                        'url' => route('video::view', ['id'=> $video->id]),
                        'img' => $video->youtube_thumb_url,
                        'html' => sprintf('<em>%s</em><br><strong><i class="fas fa-fw fa-play" aria-hidden="true"></i> %s</strong>', date("M j, Y", strtotime($video->video_date)), $video->title)
                    ])

                @endforeach

            @endif

            @if($event->albums->count() > 0)

                @foreach($event->albums as $album)

                    @include('website.layouts.macros.card-bg-image', [
                        'url' => route('photo::album::list', ['id' => $album->id]),
                        'img' => $album->thumb,
                        'html' => sprintf('<em>%s</em><br><strong><i class="fas fa-fw fa-images" aria-hidden="true"></i> %s</strong>', date("M j, Y", $album->date_taken), $album->name)
                    ])

                @endforeach

            @endif

        </div>
    </div>

@endif