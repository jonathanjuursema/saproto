@extends('emails.template')

@section('body')
    <p>Dear {{ $user->name }},</p>

    <p>
        I am writing to you to let you know that a new Google Workspace account
        was automatically created for you!
    </p>

    <p>
        With this account you will get access to all the Committee Drives and
        other Google Workspace services. To make use of this account you will
        have to sync the password with your proto account. This should be done
        here:
        <a href="{{ route('login::password::sync') }}">
            {{ route('login::password::sync') }}
        </a>
        .
    </p>

    <p>
        After this you can login to Google with your Proto email and password.
    </p>

    <p>
        Next to this email you should have received an email from Google to
        accept that they may forward messages to your personal email address.
        Please accept this request by clicking the link in that email. This
        allows you to receive messages send to the shared email of the
        committee(s) you are a part of and any email aliases that might point to
        your account.
    </p>

    <p>
        We hope to have informed you well via this e-mail. Should you have any
        questions left you can always come by the Protopolis or send an email to
        the HYTTIOAOAc.
    </p>

    <p>
        Kind regards,
        <br />
        <i>The Google Workspace Clerk</i>
    </p>
@endsection
