@extends('emails.template')

@section('body')
    {!! Markdown::convert($body) !!}

    <p>&nbsp;&nbsp;</p>

    <p>---</p>

    <p>
        <sup style="line-height: 1.5">
            @if ($destination == 'list')
                You receive this e-mail because you are subscribed to one or
                more of the following e-mail lists:
                {!! App\Models\Email::getListUnsubscribeFooter($user_id, $email_id) !!}.
            @elseif ($destination == 'event' || $destination == 'event with backup')
                You receive this e-mail because you signed up for any of the
                following events as a participant, helper or by buying a ticket
                {{ $destination == 'event with backup' ? 'or you are on the backuplist' : '' }}:
                @foreach ($events as $event)
                    <br />
                    <a
                        href="{{ route('event::show', ['id' => $event->getPublicId()]) }}"
                    >
                        {{ $event->title }}
                    </a>
                @endforeach
            @elseif ($destination == 'users')
                You receive this e-mail because you have an active user account at the website of S.A. Proto.
            @elseif ($destination == 'members')
                You receive this e-mail because you have an active membership with S.A. Proto.
            @elseif ($destination == 'active members')
                You receive this e-mail because you are an active member (participate in a committee) of S.A. Proto.
            @endif
        </sup>
    </p>
@endsection
