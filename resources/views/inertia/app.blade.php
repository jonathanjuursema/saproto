<!DOCTYPE html>
<html lang="en" class="position-relative mh-100">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <meta name="theme-color" content="#C1FF00">

    <link rel="shortcut icon" href="{{ asset('images/favicons/favicon'.mt_rand(1, 4).'.png') }}"/>
    <link rel="search" type="application/opensearchdescription+xml" title="S.A. Proto"
          href="{{ route('search::opensearch') }}"/>

    @inertiaHead

    @section('opengraph')
        <meta property="og:url" content="{{ Request::url() }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="@yield('page-title','Website')"/>
        <meta property="og:description"
              content="@yield('og-description','S.A. Proto is the study association for Creative Technology at the University of Twente.')"/>
        <meta property="og:image"
              content="@yield('og-image',asset('images/logo/og-image.png'))"/>
    @show

    {{--    @include('website.assets.stylesheets')--}}
</head>

<body>

{{--@include('website.navbar')--}}

@inertia
{{--@include('website.footer')--}}


<!-- Global scripts -->
{{--@include('website.assets.javascripts')--}}

@routes(nonce: csp_nonce())
@vite('resources/js/inertia.ts')

<script src="https://kit.fontawesome.com/63e98a7060.js" crossorigin="anonymous"></script>

</body>

</html>