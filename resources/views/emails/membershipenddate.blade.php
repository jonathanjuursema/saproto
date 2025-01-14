@extends('emails.template')

@section('body')
    <p>Dear {{ $user->name }},</p>

    <p>
        I am writing to you to let you know that the secretary has set an end
        date for your membership at S.A. Proto.
    </p>

    <p>
        Your membership will end at:
        <b>
            {{ Carbon::createFromTimestamp($user->member->until)->format('d-m-Y') }}.
        </b>
        <br />
        This means that your membership is valid up until this date and will
        then be terminated. If you think this was a mistake and would like your
        membership to continue please contact me before this date. When you're
        membership actually ends you will get a follow-up e-mail from me.
    </p>

    <p>
        I hope to have informed you well via this e-mail, but should you have
        any questions left you can always come by at the Protopolis or send me
        an e-mail.
    </p>

    <p>
        Kind regards,
        <br />
        {{ Config::string('proto.secretary') }}
        <br />
        <i>On behalf of the board of Study Association Proto</i>
    </p>
@endsection
