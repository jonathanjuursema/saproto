@extends('emails.template')

@section('body')

    <p>
        Hey!
    </p>

    <p>
        I just ran the membership fee program. The following people had their membership fee charged.
    </p>

    @if (count($charged_fees->regular) > 0)
        <p>
            <strong>Members paying the regular fee</strong>
        <ul>
            @foreach($charged_fees->regular as $user)
                <li>{{ $user }}</li>
            @endforeach
        </ul>
        </p>
    @endif

    @if (count($charged_fees->reduced) > 0)
        <p>
            <strong>Other people paying the reduced fee</strong>
        <ul>
            @foreach($charged_fees->reduced as $user)
                <li>{{ $user }}</li>
            @endforeach
        </ul>
        </p>
    @endif

    @if (count($charged_fees->remitted) > 0)
        <p>
            <strong>Exceptions who don't pay membership fee, resolve manually as needed</strong>
        <ul>
            @foreach($charged_fees->remitted as $user)
                <li>{{ $user }}</li>
            @endforeach
        </ul>
        </p>
    @endif

    <p>
        Kind regards,
        The Membership Fee Clerk
    </p>

@endsection
