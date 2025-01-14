@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $newsitem->title }}
@endsection

@section('container')
    @if ($preview)
        <div class="alert alert-info" role="alert">
            You are currently previewing an unpublished news item.
        </div>
    @endif

    <div class="row">
        <div class="{{ count($events) > 0 ? 'col-8' : '' }}">
            <div class="card mb-3">
                @if ($newsitem->featuredImage)
                    <img
                        class="card-img-top"
                        src="{{ $newsitem->featuredImage->generateImagePath('1500', '350') }}"
                    />
                @endif

                <div class="card-body">
                    <h5 class="card-title">@yield('page-title')</h5>

                    <footer class="blockquote-footer">
                        Published
                        <span title="{{ $newsitem->published_at }}">
                            {{ Carbon::parse($newsitem->published_at)->diffForHumans() }}
                        </span>
                        by
                        <a
                            href="{{ route('user::profile', ['id' => $newsitem->user->getPublicId()]) }}"
                        >
                            {{ $newsitem->user->name }}
                        </a>
                    </footer>

                    <p class="card-text">
                        {!! $parsedContent !!}
                    </p>
                </div>
            </div>
        </div>

        @if (count($events) > 0)
            <div class="col">
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt"></i>
                        Related Events
                    </div>
                    <div class="card-body">
                        @foreach ($events as $counter => $event)
                            @include(
                                'event.display_includes.event_block',
                                [
                                    'event' => $event,
                                    'lazyload' => $counter > 12,
                                ]
                            )
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
