@extends('emails.template')

@section('body')
    <p>Hey {{ $user->calling_name }},</p>

    <p>
        We're writing to let you know the e-mail address of your Proto account
        has been changed by {{ $changer['name'] }} from IP address
        {{ $changer['ip'] }}. Your e-mail address is changed from
        <i>{{ $email['old'] }}</i>
        to
        <i>{{ $email['new'] }}</i>
        .
    </p>

    <p>
        If something went wrong in changing your e-mail address and you cannot
        access your account anymore, please contact the S.A. Proto board via
        <a href="mailto:board@proto.utwente.nl">board@proto.utwente.nl</a>
        for assistance. If you believe your account has been compromised, please
        contact the webmasters directly via
        <a href="mailto:security@proto.utwente.nl">security@proto.utwente.nl</a>
        .
    </p>

    <p>
        Kind regards,
        <br />
        The Have You Tried Turning It Off And On Again committee
    </p>
@endsection
