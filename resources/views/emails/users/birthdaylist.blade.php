@extends('emails.template')

@section('body')
    <p>Hey,</p>

    <p>
        Here's a list with all the jarige joppen today. Don't forget to give
        them their cookie!
    </p>

    <p>
        @foreach ($users as $user)
            <a href="{{ route('user::profile', ['id' => $user['id']]) }}">
                {{ $user['name'] }}
            </a>
            turns {{ $user['age'] }}
            <br />
        @endforeach
    </p>

    <p>Kind regards, The Birthday Bot</p>
@endsection
