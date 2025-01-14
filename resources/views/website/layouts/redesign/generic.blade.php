@extends('website.master')

@section('body')
    @include('website.navbar')

    <div class="w-100" style="height: 71px">&nbsp;</div>

    <main role="main" class="container-fluid">
        @include('website.announcements')

        @include('website.errors')

        @yield('container')
    </main>

    @include('website.footer')
@endsection
