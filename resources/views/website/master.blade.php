<!DOCTYPE html>
<html lang="en" class="position-relative mh-100">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta
            name="viewport"
            content="initial-scale=1, maximum-scale=1, user-scalable=no"
        />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <meta name="theme-color" content="#C1FF00" />

        <link
            rel="shortcut icon"
            href="{{ asset('images/favicons/favicon' . mt_rand(1, 4) . '.png') }}"
        />
        <link
            rel="search"
            type="application/opensearchdescription+xml"
            title="S.A. Proto"
            href="{{ route('search::opensearch') }}"
        />

        <title>
            @if (! App::environment('production')) 
            [{{ strtoupper(config('app.env')) }}]
            @endif S.A.
            Proto | @yield('page-title', 'Default Page Title')
        </title>

        @stack('head')

        @include('website.assets.stylesheets')

        @stack('stylesheet')

        @section('opengraph')
            <meta property="og:url" content="{{ Request::url() }}" />
            <meta property="og:type" content="website" />
            <meta
                property="og:title"
                content="@yield('page-title', 'Website')"
            />
            <meta
                property="og:description"
                content="@yield('og-description', 'S.A. Proto is the study association for Creative Technology at the University of Twente.')"
            />
            <meta
                property="og:image"
                content="@yield('og-image', asset('images/logo/og-image.png'))"
            />
        @endsection()

        @yield('opengraph')
    </head>

    <body class="template-{{ $viewName }}">
        @yield('body')

        @if (! App::isDownForMaintenance())
            @include('components.modals.flashmessages')

            <div
                class="position-absolute top-0 start-50"
                style="margin-top: 70px"
                id="alert-wrapper"
            ></div>

            @include('components.modals.achievement-popup')

            @include('components.modals.errormessages')

            <!-- Modals -->
            @stack('modals')

            <!-- Global scripts -->
            @include('website.assets.javascripts')

            <!-- Page scripts -->
            @stack('javascript')
        @endif
    </body>
</html>
