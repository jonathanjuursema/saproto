@extends('emails.template')

@section('body')
    <p>
        Hey {{ App\Models\User::orderByRaw('RAND()')->first()->calling_name }},
    </p>

    <p>
        You're receiving this message because an administrator of the S.A. Proto
        website wants to verify whether the e-mailing service is functioning
        properly. If you think you received this e-mail by mistake you can
        simply ignore it (this was a one-of e-mail) or reply to this e-mail to
        ask for clarification.
    </p>

    <p>
        As an additional remark, the name chosen to preface this e-mail is
        randomly generated. It is not necessarily yours!
    </p>

    <p>
        Kind regards,
        <br />
        The Have You Tried Turning It Off And On Again committee
    </p>
@endsection
