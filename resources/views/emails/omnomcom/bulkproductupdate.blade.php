@extends('emails.template')

@section('body')
    <p>Hey there!</p>

    <p>
        {{ $user->name }} just did a bulk update of product stock information.
        The changelog can be found below.
    </p>

    <p>
        {!! $log !!}
    </p>

    <p>Kind regards, The System</p>
@endsection
