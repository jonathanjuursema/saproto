@extends('emails.template')

@section('body')
    <p>Hey {{ $user->calling_name }},</p>

    <p>
        @if ($user->member)
            You're receiving this e-mail as a reminder that you are a member of S.A. Proto and that, as a consequence,
            we process personal information about you.
        @else
            You're receiving this e-mail as a reminder that you still have an
            active user account at the website of S.A. Proto.
            <strong>This does not mean you are a member of S.A. Proto.</strong>
            Because you have a user account, we process some personal
            information about you.
        @endif
        We would like to ask you to verify that your personal information is up
        to date. Below you will find a summary of the most important personal
        information. To view all information about you we process, please visit
        your
        <a href="{{ route('user::dashboard::show') }}" target="_blank">
            dashboard
        </a>
        on the website. There you can also update most of the information.
    </p>

    <p>
        Name:
        <strong>{{ $user->name }}</strong>
        <br />
        E-mail:
        <strong>{{ $user->email }}</strong>
        <br />
        @if ($user->phone)
            Phone:
            <strong>{{ $user->phone }}</strong>
            @if ($user->phone_visible && $user->member)
                (Shown to other members.)
            @endif

            <br />
        @endif

        @if ($user->birthday)
            Birthday:
            <strong>{{ $user->birthday }}</strong>
        @endif

        @if ($user->edu_username)
            University account:
            <strong>{{ $user->edu_username }}</strong>
        @endif

        @if ($user->address)
            <p>
                <strong>Address</strong>
                <br />
                {{ $user->address->street }} {{ $user->address->number }}
                <br />
                {{ $user->address->zipcode }}, {{ $user->address->city }}
                <br />
                {{ $user->address->country }}
            </p>
        @endif

        @if ($user->bank)
            <p>
                <strong>SEPA direct withdrawal authorization</strong>
                <br />
                IBAN:
                <strong>{{ $user->bank->iban }}</strong>
                <br />
                BIC:
                <strong>{{ $user->bank->bic }}</strong>
                <br />
                Authorization ID:
                <strong>{{ $user->bank->machtigingid }}</strong>
                <br />
                Authorization given:
                <strong>{{ $user->bank->created_at }}</strong>
            </p>
        @endif
    </p>

    <p>
        @if ($user->member)
            If you want us to stop processing your information altogether, you
            can terminate your membership and delete your user account
            afterwards. You can contact the
            <a href="mailto:board@proto.utwente.nl">association board</a>
            to terminate your membership, and delete your account from your
            dashboard as soon as your membership has been terminated.
        @else
                If you want us to stop processing your information altogether,
                you can delete your account from your dashboard.
        @endif
        Please be reminded that while using the website, you may have generated
        some information (like activity participation, purchases and committee
        participations) that will not be deleted when you delete your user
        account. You can visit our
        <a
            href="https://wiki.proto.utwente.nl/ict/privacy/details"
            target="_blank"
        >
            privacy policy
        </a>
        to learn what information we keep and why. You can also delete most
        information without deleting your account. If you choose to delete your
        account, remember that you will also be unsubscribed from any of the
        newsletters (like the alumni newsletter) that you might have been
        subscribed to.
    </p>

    <p>We send you this reminder once per year.</p>

    <p>
        Kind regards,
        <br />
        The Have You Tried Turning It Off And On Again committee
    </p>
@endsection
