@extends('emails.template')

@section('body')
    <p>Dear {{ $user->name }},</p>

    <p>
        You receive this e-mail because you, or somebody else, used this e-mail
        address while signing up for an account on the website of
        <a href="{{ route('homepage') }}">Study Association Proto</a>
        . If this wasn't you, please reply to this e-mail and we'll sort
        everything out. If it was you, great! Welcome! You will receive a
        separate e-mail with instructions on how to set your password.
    </p>

    <p>
        Just as a heads-up, registering an account doesn't make you a member of
        Study Association Proto. If you're interested in becoming a member,
        please do follow the instructions over
        <a href="{{ route('becomeamember') }}">here</a>
        . Should this e-mail raise any questions, please don't hesitate to get
        in touch.
    </p>

    <p>
        Please note: by creating a user account you accepted the
        <a
            href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf"
            target="_blank"
        >
            privacy policy
        </a>
        of S.A. Proto. Please know that if you want to be updated when changes
        to the privacy policy are made, you should subscribe for notifications
        in the
        <i>e-mail lists</i>
        section of your
        <a href="{{ route('user::dashboard::show') }}">dashboard</a>
        .
    </p>

    <p>
        Kind regards,
        <br />
        S.A. Proto
    </p>
@endsection
