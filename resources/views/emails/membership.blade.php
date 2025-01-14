@extends('emails.template')

@section('body')
    <p>Dear {{ $user->calling_name }},</p>

    <p>
        Welcome as the newest member of Study Association Proto! My name is
        {{ Config::string('proto.internal') }} and I would like to give a
        little introduction to the association. The room where you probably just
        signed up in is our association room called the Protopolis. In this room
        we offer free coffee and tea and sell other foods and drinks via the
        OmNomCom. At the OmNomCom you can also register various RFID-enabled
        cards for fast checkout.
    </p>

    <p>
        As an association we also organise a lot of activities, these activities
        are very diverse and can be both fun and educative. You can sign up for
        these activities on our
        <a href="https://www.proto.utwente.nl">website</a>
        . We also have membership passes. You can pick yours up at the
        Protopolis and use it to get discounts at various stores and
        food-chains.
    </p>

    <p>
        With your membership also comes a Proto username. Your Proto username is
        <strong>{{ $user->member->proto_username }}</strong>
        . You can use this username instead of your e-mail address to log-in.
    </p>

    <p>
        For other Proto services outside the website, you have to use this
        username and your Proto password to log-in. On these other services,
        your University of Twente account or your e-mail address don't work.
        Before you can start using your Proto username outside of the website
        you'll need to activate your username. You do this by synchronizing your
        password to your username
        <a href="{{ route('login::password::sync::index') }}">here</a>
        .
    </p>

    <p>
        Please note: as a member of S.A. Proto you accept our
        <a
            href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf"
            target="_blank"
        >
            privacy policy
        </a>
        . Please know that if you want to be updated when changes to the privacy
        policy are made, you should subscribe for notifications in the
        <i>e-mail lists</i>
        section of your
        <a href="{{ route('user::dashboard::show') }}">dashboard</a>
        .
    </p>

    <p>
        I hope to have informed you well via this e-mail, but should you have
        any questions left you can always come by at the Protopolis or send me
        an e-mail.
    </p>

    <p>
        Kind regards,
        <br />
        {{ Config::string('proto.internal') }}
        <br />
        <i>On behalf of the board of Study Association Proto</i>
    </p>
@endsection
