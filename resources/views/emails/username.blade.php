@extends('emails.template')

@section('body')
    <p>Dear {{ $name }},</p>

    <p>
        You receive this e-mail because you requested a reminder for your
        username with the S.A. Proto website.

        @if ($ismember)
            <p>
                Your username with the S.A. Proto website is
                <strong>{{ $username }}</strong>
                .
            </p>
        @else
            <p>
                You are not a member of S.A. Proto and therefore do not have a
                username. You can, however, just log-in with your e-mail address
                as your username!
            </p>
        @endif
    </p>

    <p>
        Kind regards,
        <br />
        The Have You Tried Turning It Off And On Again committee
    </p>
@endsection
