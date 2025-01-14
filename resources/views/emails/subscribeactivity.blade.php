@extends('emails.template')

@section('body')
    <p>
        Hey {{ $activity['name'] }},

        @if ($activity['help'] !== null)
            <p>
                You receive this e-mail because the board signed you up to help
                with
                <a
                    href="{{ route('event::show', ['id' => $activity['id']]) }}"
                >
                    {{ $activity['title'] }}
                </a>
                as part of the {{ $activity['help'] }}. If you believe this is
                a mistake, please let us know or head over to the website and
                sign out of the activity.
            </p>
        @else
            <p>
                You receive this e-mail because the board signed you up for
                <a
                    href="{{ route('event::show', ['id' => $activity['id']]) }}"
                >
                    {{ $activity['title'] }}
                </a>
                . If you believe this is a mistake, please let us know or head
                over to the website and sign out of the activity.
            </p>
        @endif
    </p>

    <p>
        Kind regards,
        <br />
        The board of Study Association Proto
    </p>
@endsection
