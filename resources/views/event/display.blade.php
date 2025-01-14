@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $event->title }}
@endsection

@section('og-description')
    From {{ $event->generateTimespanText('l j F Y, H:i', 'H:i', 'till') }} @
    {{ $event->location }}.

    {{ $event->description }}
@endsection

@if ($event->image)
    @section('og-image')
        {{ $event->image->generateImagePath(800, 300) }}
    @endsection
@endif

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            @include(
                'event.display_includes.event_details',
                [
                    'event' => $event,
                ]
            )
        </div>

        @if (Auth::check() && ($event->activity?->withParticipants() || $event->tickets()->count() > 0))
            <div class="col-md-4">
                @include(
                    'event.display_includes.tickets',
                    [
                        'event' => $event,
                        'payment_methods' => $payment_methods,
                    ]
                )

                @include(
                    'event.display_includes.participation',
                    [
                        'event' => $event,
                    ]
                )
            </div>
        @endif

        @if ((Auth::user()?->is_member && $event->activity?->helpingCommitteeInstances->count()) || $event->dinnerforms()->count())
            <div class="col-md-4">
                @if ($event->activity?->helpingCommitteeInstances->count() && Auth::user()?->is_member)
                    <div class="card mb-3">
                        @include(
                            'event.display_includes.helpers',
                            [
                                'event' => $event,
                            ]
                        )
                    </div>
                @endif

                @if ($event->dinnerforms()->count())
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white">
                            <i class="fas fa-utensils fa-fw me-2"></i>
                            Dinnerform
                        </div>
                        <div class="card-body">
                            @foreach ($event->dinnerforms()->get() as $dinnerform)
                                @include('dinnerform.includes.dinnerform-block', ['dinnerform' => $dinnerform])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
