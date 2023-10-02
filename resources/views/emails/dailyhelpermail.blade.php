@extends('emails.template')

@section('body')

    <p>
        Hey {{ $user->calling_name }},
    </p>

    <p>
        You receive this e-mail because you are needed by one or more activities! The activities that need your help are
        listed below.
    </p>

    @php $i = 0; @endphp

    @foreach($events as $eventId => $event)

        @php

            // This is done in this template in order to prevent the jobs table from containing large amounts of event data.
            $event->event = App\Models\Event::withTrashed()->find($eventId);

        @endphp

        <table style="margin: 0; padding: 0; border: none; background-color: {{ ($i % 2 == 0 ? '#f0f0f0' : '#fff') }};"
               width="100%">

            <tr style="margin: 0; padding: 0; border: none;">

                <td style="margin: 0; padding: 20px 40px; border: none;">

                    @if($event->event->image)
                        <img src="{{ $event->event->image->generateImagePath(350,100) }}" style="width: 100%;"/>
                    @endif

                    <p>
                        <strong>{{ $event->event->title }}</strong> @ {{ $event->event->location }}<br>
                        {{ date('l d F, H:i', $event->event->start) }}
                        - {{ ($event->event->end - $event->event->start >= 3600*24 ? date('l d F, H:i', $event->event->end) : date('H:i', $event->event->end)) }}
                    </p>

                    @foreach($event->help as $help)

                        <p>
                            The organization of <i>{{ $event->event->title }}</i>
                            indicated they need <strong>{{ $help->amount }}</strong> people from the
                            <strong>{{ $help->committeeName }}</strong> (of which
                            you are a member) to help them out during the activity.
                        </p>

                    @endforeach

                    <p>
                        If you feel inclined to help, please
                        check the
                        <a href="{{ route('event::show', ['id' => $event->event->getPublicId()]) }}">activity
                            page</a> for more
                        information and to indicate whether you want to help.
                    </p>

                </td>

            </tr>

        </table>

        @php $i++; @endphp

    @endforeach

    <p>
        Kind regards,
        <br>
        The board of Study Association Proto
    </p>

@endsection
