@extends('emails.template')

@section('body')
    <p>Hey {{ $calling_name }},</p>

    <p>
        You receive this e-mail because you have signed up for
        <a href="{{ route('event::show', ['id' => $event_id]) }}">
            {{ $event_title }}
        </a>
        and were placed on the back-up list. We are happy to tell you that you
        have been moved from the back-up list to the participant list! You are
        now elegible to participate in the activity!
    </p>

    <p>
        If you don't want to participate in this activity anymore, please head
        over to the website and sign out for the activity.
    </p>

    <p>
        Kind regards,
        <br />
        The board of Study Association Proto
    </p>
@endsection
