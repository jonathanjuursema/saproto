@extends('website.layouts.redesign.generic')

@section('page-title')
    Archive for {{ $year }}
@endsection

@section('container')
    @include('event.calendar_includes.archivebar')

    <div class="row justify-content-center">
        @foreach ($eventsPerMonth as $monthNumber => $events)
            @if (date('F Y', strtotime($year . '-' . $monthNumber . '-25')) < date('U') || count($events) > 0)
                @include(
                    'event.calendar_includes.rendermonth',
                    [
                        'events' => $events,
                        'month_name' => date(
                            'F Y',
                            strtotime($year . '-' . $monthNumber . '-25'),
                        ),
                    ]
                )
            @endif
        @endforeach
    </div>
@endsection
