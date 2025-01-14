@extends('website.layouts.content')

@section('container')
    <div id="container" class="container container-nobg">
        @include('website.announcements')

        @yield('content')
    </div>
@endsection
