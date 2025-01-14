@extends('emails.template')

@section('body')
    <p>
        Hey!

        @if (count($unlinked) > 0)
            <p>
                I just checked the validity of UTwente accounts. I found
                {{ count($unlinked) }} inactive accounts:
            </p>

            <ul>
                @foreach ($unlinked as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @else
            <p>
                I just checked the validity of UTwente accounts. All accounts
                seem to be active.
            </p>
        @endif
    </p>

    <p>Kind regards, The System</p>
@endsection
