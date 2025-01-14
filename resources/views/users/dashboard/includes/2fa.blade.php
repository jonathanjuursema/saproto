<div class="card mb-3">
    <div class="card-header bg-dark text-white">Two-factor authentication</div>

    <div class="card-body">
        <a id="2fa"></a>

        @if ($user->tfa_totp_key)
            <p class="card-text text-center">
                You have two-factor authentication enabled.
            </p>
        @else
            <p class="card-text">
                Two-factor authentication adds a second authentication factor,
                other than your password, to your account. This makes your
                account more secure against people guessing your password or
                using your computer with a password manager open. You will need
                to use this second authentication factor whenever you sign in to
                the website.
            </p>

            <p class="card-text">
                You can use any two-factor authentication app with Proto. Good
                recommendations are Google Authenticator or Authy. Look for them
                in your device's app store.
            </p>
        @endif
    </div>

    <div class="card-footer">
        @if ($user->tfa_totp_key)
            <div
                data-bs-toggle="modal"
                data-bs-target="#totp-modal-disable"
                class="btn btn-outline-danger btn-block"
            >
                Disable two-factor authentication
            </div>
        @else
            <div
                data-bs-toggle="modal"
                data-bs-target="#totp-modal"
                class="btn btn-outline-info btn-block"
            >
                Configure Two-Factor Authentication
            </div>
        @endif

        @include('users.2fa.timebased')
    </div>
</div>
