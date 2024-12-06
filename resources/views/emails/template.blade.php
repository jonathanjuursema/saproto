<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style> img {
            width: 100%
        } </style>
    <link rel="shortcut icon" href="{{ asset('images/favicons/favicon'.mt_rand(1, 4).'.png') }}" />
    <link rel="search" type="application/opensearchdescription+xml" title="S.A. Proto"
          href="{{ route('search::opensearch') }}" />

    <title>@if(!App::environment('production'))
            [{{ strtoupper(config('app.env')) }}]
        @endif S.A. Proto
        | @yield('page-title','Default Page Title')</title>

    @include('website.assets.stylesheets')

    @stack('stylesheet')
</head>

<body class="bg-body template-{{ $viewName }}" style="margin: 0; padding: 0; font-family: Arial, sans-serif;">

<br><br><br>

<table class="table table-light table-borderless"
       style="width: 500px ; margin: 0 auto; padding: 0; border-top: 5px solid #83b716;">
    <tr style="padding: 0; margin: 0;">
        <td style="padding: 20px 40px; text-align: justify; width:500px">
            @yield('body')
        </td>
    </tr>
</table>

<table class="table table-dark table-borderless" style="width: 500px; margin: 0 auto 40px auto; padding: 20px 40px;">
    <tr style="padding: 0; margin: 0;">
        <td style="line-height: 1.2; padding-left: 40px; padding-top:20px" width="50%">
            <strong>
                <span style="color: #83b716;">S.A. Proto</span>
            </strong>
            <br>
            Zilverling A230<br>
            Drienerlolaan 5<br>
            7522NB Enschede<br>
        </td>
        <td style="line-height: 1.2; padding: 0;">
            <br>
            Mon&Fri, 08:30-16:00<br>
            Tue-Thu, 08:30-17:30<br>
            <a style="text-decoration: none;" href="tel:+31534894423">+31 (0)53 489 4423</a><br>
            <a style="text-decoration: none;" href="mailto:board@proto.utwente.nl">
                board@proto.utwente.nl
            </a>
        </td>
    </tr>
    <tr style="padding: 0; margin: 0;">
        <td style="margin: 0; padding-left: 40px;" colspan="2">
            <br>
            <sup style="line-height: 1.5;">
                If you feel that you should not have received this e-mail, please contact
                <a href="mailto:abuse@proto.utwente.nl" style="text-decoration: none;">abuse@proto.utwente.nl</a>.
            </sup>
        </td>
    </tr>
</table>

<div style="text-align: center;">
    <a href="{{route('homepage')}}">
        @if(Auth::check() && Auth::user()->theme)
            <img
                src="{{ asset('images/logo/'.Config::array('proto.logoThemes')[Auth::user()->theme].'.png') }}"
                style="width: 30%; max-width: 200px;" alt="ProtoLogo" />
        @else
            <img src="{{ asset('images/logo/regular.png') }}" style="width: 30%; max-width: 200px;" alt="ProtoLogo" />
        @endif
    </a>
</div>

<br><br><br>

<hr style="border: none;">

</body>

</html>
