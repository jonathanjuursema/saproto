@extends('auth.template')

@push('head')
    {!! htmlScriptTagJsApi() !!}
@endpush

@section('page-title')
    Register account
@endsection

@section('login-body')
    <form
        method="POST"
        action="{{ route('login::register') }}"
        class="text-start"
    >
        @if (Session::get('wizard'))
            @include('users.registerwizard_macro')
        @endif

        {{-- temporarily disabled because the creation of an account with a university account is not fully working --}}

        {{--
            <a href="{{ route('login::edu') }}" class="btn btn-success w-100">
            Create an account with your university account
            </a>
            
            <hr>
        --}}

        @if (! Session::get('wizard'))
            <p>
                Using this form you can register a new account on the S.A. Proto
                website.
            </p>

            <p class="fw-bold">
                Creating and having an account on the website does not make you
                a member of S.A. Proto and is free of charge.
            </p>

            <hr />
        @endif

        @csrf

        <input
            type="text"
            class="form-control mb-3"
            id="email"
            name="email"
            placeholder="Your e-mail address"
            required
            value="{{ Session::has('register_persist') ? Session::get('register_persist')['email'] : '' }}"
        />

        <p>
            Your e-mail address will also be your username. Please enter a valid
            e-mail address as your password will be sent to this e-mail address.
            <br />
            <i>
                Note: For practical reasons you cannot set your e-mail address
                to an ".utwente.nl" account.
            </i>
        </p>

        <hr />

        <input
            type="text"
            class="form-control mb-1"
            id="name"
            name="name"
            placeholder="Full name"
            required
            value="{{ Session::has('register_persist') ? Session::get('register_persist')['name'] : '' }}"
        />

        <input
            type="text"
            class="form-control"
            id="calling_name"
            name="calling_name"
            placeholder="Calling name"
            required
            value="{{ Session::has('register_persist') ? Session::get('register_persist')['calling_name'] : '' }}"
        />

        <hr />

        <a
            href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf"
            target="_blank"
            class="btn btn-outline-secondary btn-block mb-2"
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

        <hr />

        {!! htmlFormSnippet() !!}

        <hr />

        <button type="submit" class="btn btn-success btn-block">
            Create my account
        </button>
    </form>
@endsection
