@extends('emails.template')

@section('body')

    <p>
        Hey {{ $calling_name }},
    </p>

    <p>
        You receive this e-mail because someone else signed you out as helping on behalf of the
        <i>{{ $committee_name }}</i> at
        <a href="{{ route('event::show', ['id' => $event_id]) }}">
            {{ $event_title }}
        </a>.
        If you believe this is a mistake, please let us know!
    </p>

    <p>
        Kind regards,
        <br>
        The board of Study Association Proto
    </p>

@endsection