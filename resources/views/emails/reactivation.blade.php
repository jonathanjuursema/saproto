@extends('emails.template')

@section('body')
    <p>Dear {{ $user->name }},</p>

    <p>
        You receive this e-mail because your account on the website of
        <a href="{{ route('homepage') }}">Study Association Proto</a>
        has been re-activated. Welcome back! You will receive a separate e-mail
        with instructions on how to set your password.
    </p>

    <p>
        Just as a heads-up, registering an account doesn't make you a member of
        Study Association Proto. If you're interested in becoming a member,
        please do follow the instructions over
        <a href="{{ route('becomeamember') }}">here</a>
        . Should this e-mail raise any question, please don't hestitate to get
        in touch.
    </p>

    <p>
        Kind regards,
        <br />
        The board of Study Association Proto
    </p>
@endsection
