@extends('website.master')

@section('header')
    <div id="header" class="main__header">
        <div class="container">
            <h1>
                <span>
                    @yield('page-title')
                </span>
            </h1>
        </div>
    </div>
@endsection

@section('body')
    @include('website.navbar')

    @yield('header')

    @yield('container')
@endsection
