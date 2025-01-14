@extends('emails.template')

@section('body')
    <p>Hey there! Your committee just received an anonymous e-mail.</p>

    <hr />

    <p>
        {{ $message_content }}
    </p>

    <hr />

    <p>
        <i>
            If this is spam, please contact the Have You Tried Turning It Off
            And On Again committee. E-mail hash: {{ $hash }}
        </i>
    </p>
@endsection
