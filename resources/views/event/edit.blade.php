@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $event == null ? 'Create new event.' : 'Edit ' . $event->title }}
@endsection

@section('container')
    <div class="row">
        <div class="col-md-8">
            @include('event.edit_includes.event-details')
        </div>

        <div class="col-md-4">
            @include('event.edit_includes.copy')

            @include('event.edit_includes.activity')

            @include('event.edit_includes.albums')

            @include('event.edit_includes.helpers')
        </div>
    </div>
@endsection
