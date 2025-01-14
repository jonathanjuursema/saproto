@extends('emails.template')

@section('body')
    <p>Dear {{ $user->name }},</p>

    <p>
        I'm writing to let you know that we will soon renew your membership for
        academic year {{ date('Y') }}/{{ date('Y') + 1 }}. If you wish to
        continue your membership of S.A. Proto, there is nothing you need to do!
        Please remember that this means you will automatically be charged your
        membership fee around October 1.
    </p>

    <p>
        If you would like to cancel your membership with S.A. Proto, please let
        us know by replying to this e-mail before September 1. If you do so your
        membership will be terminated immediately and you will not be charged
        the membership fee for academic year
        {{ date('Y') }}/{{ date('Y') + 1 }}. If you don't reply to this e-mail
        before September 1, we will assume that you wish to continue your
        membership for academic year {{ date('Y') }}/{{ date('Y') + 1 }}.
    </p>

    <p>
        I hope to have informed you well via this e-mail, but should you have
        any questions left you can always come by at the Protopolis or send me
        an e-mail.
    </p>

    <p>
        Kind regards,
        <br />
        {{ Config::string('proto.secretary') }}
        <br />
        <i>On behalf of the board of Study Association Proto</i>
    </p>
@endsection
