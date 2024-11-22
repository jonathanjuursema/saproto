@extends('emails.template')

@section('body')

    <p>
        Hi {{ $user->calling_name }},
    </p>

    <p>
        Happy birthday! Because we are so excited it is the day you were born, you can get a free cookie! Just hop by
        the Protopolis and ask a board member for your birthday cookie and we will be glad to help you out.
    </p>

    <img alt="The board wishing you a happy birthday" src="{{ asset('images/emails/birthday.jpg') }}">

    <p>
        Have a great day,
        Board {{ Config::string('proto.boardnumber') }}
    </p>

@endsection
