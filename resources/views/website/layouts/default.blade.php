@extends('website.layouts.content')

@section('container')
    <div id="container" class="container">
        @include('website.announcements')

        @yield('content')
    </div>
@endsection
