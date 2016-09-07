@extends('emails.template')

@section('body')

    <p>
        Hey {{ $participation->user->name_first }},
    </p>

    <p>
        You receive this e-mail because someone else signed you out for
        <a href="{{ route('event::show', ['id' => $participation->activity->event->id]) }}">
            {{ $participation->activity->event->title }}
        </a>.
        If you believe this is a mistake, please let us know!
    </p>

    <p>
        Kind regards,
        <br>
        The board of Study Association Proto
    </p>

@endsection