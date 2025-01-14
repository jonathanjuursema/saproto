@extends('website.master')

@section('body')
    <main role="main" id="nonavandfooter" class="container-fluid pt-3">
        @include('website.announcements')

        @yield('container')
    </main>
@endsection
