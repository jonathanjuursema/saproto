@extends('emails.template')

@section('body')
    <p>Hey {{ $activity['name'] }},</p>

    <p>
        You receive this e-mail because the board signed you out for
        <a href="{{ route('event::show', ['id' => $activity['id']]) }}">
            {{ $activity['title'] }}
        </a>
        . If you believe this is a mistake, please let us know.
    </p>

    <p>
        Kind regards,
        <br />
        The board of Study Association Proto
    </p>
@endsection
