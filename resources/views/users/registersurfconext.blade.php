@extends('auth.template')

@section('page-title')
    Register account
@endsection

@section('login-body')
    <form
        method="POST"
        class="text-justify"
        action="{{ route('login::register::surfconext') }}"
    >
        @if (Session::get('wizard'))
            @include('users.registerwizard_macro')
        @else
            <p>
                You tried to login using a university account we don't
                recognize. If you wish to create an account on the Proto
                website, you can use this here.
            </p>

            <p class="fw-bold">
                Creating and having an account on the website does not make you
                a member of S.A. Proto and is free of charge.
            </p>

            <hr />
        @endif

        @csrf

        <p>Create an account using the following details:</p>

        <p>
            Name:
            <strong>
                {{ $remote_data['givenname'] }} {{ $remote_data['surname'] }}
            </strong>
        </p>

        <p>
            E-mail address:
            <strong>{{ $remote_data['mail'] }}</strong>
            <br />
            <i>You can change this later!</i>
        </p>

        <p>
            University account:
            <strong>{{ $remote_data['uid'] }}</strong>
            at
            <strong>{{ $remote_data['org'] }}</strong>
            <br />
            <i>You can unlink your account later!</i>
        </p>

        <hr />

        <p>
            Although you can log-in to your account using your university
            account, you will also receive an e-mail that'll allow you to set a
            password. You should do this, because you can also use that password
            in combination with your e-mail address to log-in, and you can't use
            your university account everywhere where you log-in to your account.
        </p>

        <hr />

        <p>
            <a
                href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf"
                target="_blank"
                class="btn btn-outline-secondary btn-block"
            >
                Privacy policy
            </a>

            @include(
                'components.forms.checkbox',
                [
                    'name' => 'privacy_policy_acceptance',
                    'label' => 'I have read and acknowledge the privacy policy.',
                    'required' => true,
                ]
            )
        </p>

        <hr />

        <button type="submit" class="btn btn-success float-end">
            Create my account
        </button>
    </form>
@endsection
