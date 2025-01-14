@extends('website.layouts.redesign.generic')

@section('page-title')
    Calendar
@endsection

@section('container')
    @include('event.calendar_includes.archivebar')

    <div class="row calendar justify-content-center">
        @foreach ($events as $key => $section)
            @include(
                'event.calendar_includes.rendermonth',
                [
                    'events' => $section,
                    'month_name' =>
                        $key == 0 ? 'Soon' : ($key == 1 ? 'This month' : 'Later'),
                ]
            )
        @endforeach
    </div>
@endsection
