@extends('emails.template')

@section('body')

    <p>
        Dear {{ $name }},
    </p>

    <p>
        You receive this e-mail because we found that the password you use at our website was exposed in one or more
        data breaches. This means that the password is no longer safe, and we strongly recommend <a
            href="{{ route("login::password::change::index") }}">changing your password on our website</a> and any sites
        that you also use this password for.
    </p>

    <p>
        Please note that this e-mail <strong>does not mean that S.A. Proto has been the victim of a data breach and/or
            security incident</strong>. It means that your password was exposed due to one or more data breaches on
        another website.
    </p>

    <p>
        Please read <a href="https://wiki.proto.utwente.nl/ict/pwned-passwords">this</a> article on our wiki for more
        information on how we were able to determine that your password was exposed, or if you have concerns about
        receiving this e-mail.
    </p>

    <p>
        Kind regards,
        <br>
        The Have You Tried Turning It Off And On Again committee
    </p>

@endsection
