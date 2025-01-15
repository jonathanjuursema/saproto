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
        @csrf

        <p>
            Using this form you can register a new account on the S.A. Proto
            website using your University account or without. You can always
            unlink your University account later.
        </p>

        <p class="fw-bold">
            Creating and having an account on the website does not make you a
            member of S.A. Proto and is free of charge.
        </p>

        <hr />

        <input
            type="text"
            class="form-control mb-3"
            id="email"
            name="email"
            placeholder="Your e-mail address"
            required
            value="{{ old('email') }}"
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

        <div
            id="regular-account-creation"
            {{ empty(old('create_without_ut_account')) ? 'hidden' : '' }}
        >
            <hr />
            <input
                type="text"
                class="form-control"
                id="calling_name"
                name="calling_name"
                placeholder="Calling name"
                value="{{ old('calling_name') }}"
            />
            <input
                type="text"
                class="form-control mt-1"
                id="name"
                name="name"
                placeholder="Full name"
                value="{{ old('name') }}"
            />
        </div>

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

        <div class="text-center mt-1">
            <input
                hidden
                class="form-check-input"
                id="create_without_ut_account"
                name="create_without_ut_account"
                type="checkbox"
                autocomplete="off"
                {{ ! empty(old('create_without_ut_account')) ? 'checked' : '' }}
            />
            <label
                id="toggle-ut-account-button"
                class="form-check-label text-decoration-underline cursor-pointer"
                style="font-size: 14px"
                for="create_without_ut_account"
            >
                {{
                    empty(old('create_without_ut_account'))
                        ? 'I do not have a University of Twente account'
                        : 'I have a University of Twente account'
                }}
            </label>
        </div>
    </form>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        let additionalNameInputs = document.getElementById(
            'regular-account-creation'
        )
        let toggleInput = document.getElementById('create_without_ut_account')
        let toggleButton = document.getElementById('toggle-ut-account-button')

        toggleInput.addEventListener('change', function (event) {
            let createWithUT = !event.target.checked
            additionalNameInputs.hidden = createWithUT

            // set all the inputs in the additionalNameInputs to required or not required
            additionalNameInputs
                .querySelectorAll('input')
                .forEach(function (input) {
                    input.required = !createWithUT
                })

            if (createWithUT) {
                toggleButton.textContent =
                    'I do not have a University of Twente account'
            } else {
                toggleButton.textContent =
                    'I have a University of Twente account'
            }
        })
    </script>
@endpush
