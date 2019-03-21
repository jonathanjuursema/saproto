@extends('emails.template')

@section('body')

    <p>
        Dear {{ $name }},
    </p>

    <p>
        You receive this e-mail because you requested a new password for your account on the website of Study
        Association Proto. Using the link below, you can set a new password. But be quick! The link below only works for
        a hour, after which you need to request a new link.
    </p>

    <p>
        <a href="{{ route("login::resetpass::token",['token' => $token]) }}">{{ route("login::resetpass::token",['token' => $token]) }}</a>
    </p>

    <p>
        If you did not initiate a request for a new password, no worries! Just ignore this e-mail and everything will be
        dandy.
    </p>

    <p>
        Kind regards,
        <br>
        The Have You Tried Turning It Off And On Again committee
    </p>

@endsection