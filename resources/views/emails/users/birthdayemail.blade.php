@extends('emails.template')

@section('body')

    <p>
        Hey {{ $user->calling_name }},
    </p>

    <p>
        I am writing you to congratulate you with
        your {{ (new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format($user->age()) }} birthday! If you are
        able to drop by the Protopolis today I would love to congratulate you in person and offer you a free and
        complimentary birthday cookie. Is there a Proto drink on your birthday? You can also choose to get
        a free birthday pull during the drink. Have a great day!
    </p>

    <img src="{{ asset('images/emails/birthday.jpg') }}" style="width: 100%;">

    <p>
        Kind regards,
        {{ config('proto.internal') }}
    </p>

@endsection