@if (! $committee->public)
    <div class="alert alert-info" role="alert">
        This is a hidden
        @if ($committee->is_society)
            society!
        @else
            committee!
        @endif
    </div>
@endif

@if (Auth::check() && $committee->allow_anonymous_email)
    <a
        href="{{ route('committee::anonymousmail', ['id' => $committee->getPublicId()]) }}"
        class="btn btn-block btn-info mb-3"
    >
        <i class="fas fa-envelope-open fa-fw"></i>
        Send this
        @if ($committee->is_society)
            society
        @else
            committee
        @endif
        an anonymous e-mail
    </a>
@endif

<div class="card mb-3">
    @if ($committee->image)
        <img
            class="card-img-top"
            src="{{ $committee->image->generateImagePath(800, 300) }}"
            width="100%"
        />
    @endif

    @if (Auth::check() && ($committee->isMember(Auth::user()) || Auth::user()->can('board') || $committee->allow_anonymous_email))
        <div class="card-header bg-dark">
            <div class="row justify-content-end">
                @can('board')
                    <div class="col-4">
                        <a
                            href="{{ route('committee::edit', ['id' => $committee->id]) }}"
                            class="btn btn-primary btn-block"
                        >
                            <i class="fas fa-edit fa-fw"></i>
                            Edit
                        </a>
                    </div>
                @endcan
            </div>
        </div>
    @endif

    <div class="card-body">
        <h5 class="card-title">@yield('page-title')</h5>

        <p class="card-text">
            {!! Markdown::convert($committee->description) !!}

            <a
                href="mailto:{{ $committee->slug . '@' . Config::string('proto.emaildomain') }}"
                class="card-link text-info"
            >
                E-mail them at
                {{ $committee->slug . '@' . Config::string('proto.emaildomain') }}
            </a>
        </p>
    </div>
</div>
