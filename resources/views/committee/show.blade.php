@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $committee->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('committee.include.info')
            @if (Auth::check() && Auth::user()->member)
                @include('committee.include.organised-events')
            @endif
        </div>

        @if (Auth::check() && Auth::user()->member)
            <div class="col-md-4">
                @include('committee.include.members-list')
            </div>
        @endif
    </div>
@endsection
