@extends('emails.template')

@section('body')

    {!! Markdown::convert($body) !!}

    <p>
        &nbsp;&nbsp;
    </p>

    <p>
        ---
    </p>

    <p>
        <sup style="line-height: 1.5;">
            @if($email->destination === App\Enums\EmailDestination::EMAIL_LISTS)
                You receive this e-mail because you are subscribed to one or more of the following e-mail lists:
                {!! App\Models\Email::getListUnsubscribeFooter($user_id) !!}.
            @elseif($email->destination === App\Enums\EmailDestination::EVENT || $email->destination==App\Enums\EmailDestination::EVENT_WITH_BACKUP)
                You receive this e-mail because you signed up for any of the following events as a participant, helper
                or by buying a
                ticket {{$email->destination===App\Enums\EmailDestination::EVENT_WITH_BACKUP?'or you are on the backuplist':''}}
                :
                @foreach($email->events as $event)
                    <br><a href="{{route('event::show', ['id' => $event->getPublicId()])}}"> {{$event->title}} </a>
                @endforeach
            @elseif($email->destination == App\Enums\EmailDestination::ALL_USERS)
                You receive this e-mail because you have an active user account at the website of S.A. Proto.
            @elseif($email->destination == App\Enums\EmailDestination::ALL_MEMBERS)
                You receive this e-mail because you have an active membership with S.A. Proto.
            @elseif($email->destination == App\Enums\EmailDestination::PENDING_MEMBERS)
                You receive this e-mail because you have are a pending member of S.A. Proto.
            @elseif($email->destination == \App\Enums\EmailDestination::ACTIVE_MEMBERS)
                You receive this e-mail because you are an active member (are participating in a committee) of S.A.
                Proto.
            @elseif($email->destination == \App\Enums\EmailDestination::SPECIFIC_USERS)
                You receive this e-mail because it was specifically addressed to you.
            @endif
        </sup>
    </p>

@endsection
