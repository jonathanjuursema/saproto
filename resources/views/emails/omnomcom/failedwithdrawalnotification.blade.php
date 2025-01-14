@extends('emails.template')

@section('body')
    <p>Dear {{ $user->calling_name }},</p>

    <p>
        We received word from your bank that the automatic withdrawal of
        <a
            href="{{ route('omnomcom::mywithdrawal', ['id' => $withdrawal->id]) }}"
        >
            {{ date('d-m-Y', strtotime($withdrawal->date)) }}
        </a>
        has been denied. This can be due to any number of reasons, but usually
        it is one of the following:
    </p>

    <ul>
        <li>Your account balance was insufficient.</li>
        <li>You have revoked the withdrawal authorisation you gave us.</li>
        <li>
            Your bank account is no longer
            <strong>{{ $user->bank->iban }}</strong>
            .
        </li>
    </ul>

    <p>
        We will try again during the next withdrawal. This means that in your
        order history you will find a new order of
        &euro;{{ number_format($withdrawal->totalForUser($user), 2, ',', '') }}.
    </p>

    <p>
        If your bank account details have changed, please be sure to issue a new
        withdrawal authorisation. You can do so
        <a href="{{ route('user::bank::edit', ['id' => $user->id]) }}">here</a>
        .
    </p>

    <p>
        If you have issues paying for your OmNomCom purchases, please get in
        touch with us. Together we can think of a solution.
    </p>

    <p>
        I hope to have informed you well via this e-mail, but should you have
        any questions left you can always send me a message.
    </p>

    <p>
        Kind regards,
        <br />
        {{ Config::string('proto.treasurer') }}
        <br />
        <i>On behalf of the board of Study Association Proto</i>
    </p>
@endsection
