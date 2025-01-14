@extends('website.master')

@section('body')
    <main
        role="main"
        class="container-fluid pt-3 border-top border-4 border-primary"
    >
        @include('website.announcements')

        @yield('container')
    </main>

    @include('website.footer')
@endsection
