@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Wallstreet Admin
@endsection

@section('container')
    <div class="row">
        <div class="col-xl-4">
            @if ($currentEvent)
                @include('wallstreet.admin_includes.wallstreetdrink-event-product-list')
            @endif

            @include('wallstreet.admin_includes.wallstreetdrink-event-details')
        </div>
        <div class="col-xl-8">
            @include('wallstreet.admin_includes.wallstreetdrink-event-list')
        </div>
    </div>
@endsection
