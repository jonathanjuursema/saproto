@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $new ? 'Add' : 'Update' }} a withdrawal authorization for
    {{ $user->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form
                method="POST"
                id="iban-form"
                action="{{ $new ? route('user::bank::store', ['id' => $user->id]) : route('user::bank::update', ['id' => $user->id]) }}"
            >
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        @if (Session::get('wizard'))
                            @include('users.registerwizard_macro')
                        @endif

                        @if ($user->id != Auth::id())
                            <p>
                                Sorry, but due to accountability issues you can
                                only add authorizations for yourself. If
                                {{ $user->name }} really wants to pay via
                                automatic withdrawal, they should authorize so
                                themselves.
                            </p>
                        @else
                            @if (! $new)
                                <div class="card mb-3">
                                    <div class="card-header bg-dark text-white">
                                        <strong>
                                            Your current authorization
                                        </strong>
                                    </div>

                                    <div class="card-body">
                                        <p class="card-text text-center">
                                            <strong>
                                                {{ iban_to_human_format($user->bank->iban) }}
                                            </strong>
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ iban_to_human_format($user->bank->bic) }}
                                        </p>

                                        <p class="card-text text-center">
                                            <sub>
                                                authorization issued on
                                                {{ $user->bank->created_at }}.
                                                <br />
                                                Authorization ID:
                                                {{ $user->bank->machtigingid }}
                                            </sub>
                                        </p>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div
                                        class="card-header bg-danger text-white"
                                    >
                                        <strong>Attention</strong>
                                    </div>

                                    <div class="card-body">
                                        <p class="card-text">
                                            You already have a bank
                                            authorization active. Updating your
                                            authorization will remove this
                                            authorization from the system. If an
                                            automatic withdrawal is already
                                            being processed right now, it may
                                            still be withdrawn using this
                                            authorization.
                                        </p>
                                    </div>
                                </div>

                                <hr />
                            @endif

                            @csrf
                            <div class="form-group">
                                <label for="iban">
                                    IBAN Bank Account Number
                                </label>
                                <input
                                    type="text"
                                    class="form-control text-uppercase"
                                    id="iban"
                                    name="iban"
                                    placeholder="NL42INGB0013371337"
                                />
                            </div>

                            <p>
                                <span id="iban-message">
                                    Please enter your IBAN above.
                                </span>
                            </p>

                            <hr />

                            <div class="form-group">
                                <label for="bic">Bank BIC Code</label>
                                <input
                                    type="text"
                                    class="form-control text-uppercase"
                                    id="bic"
                                    name="bic"
                                    placeholder=""
                                    required
                                />
                            </div>

                            <p>
                                <span id="bic-message">
                                    Enter your IBAN first.
                                </span>
                            </p>

                            <hr />

                            <p>
                                <strong>Authorization Statement</strong>
                            </p>

                            <p>
                                You hereby legally authorize Study Association
                                Proto until further notice to charge to your
                                bank account any costs you incur with the
                                association. These include but are not limited
                                to:
                            </p>

                            <ul>
                                <li>Orders from the OmNomCom store</li>
                                <li>Participation in activities</li>
                                <li>
                                    Other Proto related activities and products
                                    such as merchandise and printing
                                </li>
                                <li>Your membership fee</li>
                            </ul>

                            <p>
                                (Some of these may only applicable to members of
                                the association.)
                            </p>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button
                            type="button"
                            id="iban-submit"
                            class="btn btn-success float-end"
                            disabled
                        >
                            I have read the authorization statement and agree
                            with it.
                        </button>

                        <a
                            href="{{ route('user::dashboard::show') }}"
                            class="btn btn-default"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const iban = document.getElementById('iban')
        const bic = document.getElementById('bic')
        const submit = document.getElementById('iban-submit')
        const form = document.getElementById('iban-form')
        const ibanMessage = document.getElementById('iban-message')
        const bicMessage = document.getElementById('bic-message')
        console.log('Init')

        const onkey = (_) => {
            iban.value = iban.value.replace(' ', '')
            if (iban.value.length >= 15) {
                get('{{ route('api::verify_iban') }}', {
                    iban: iban.value,
                    bic: bic.value,
                })
                    .then((data) => update_iban_form(data))
                    .catch((error) => {
                        console.error(error)
                        iban_message(
                            'black',
                            'We could not automatically verify your IBAN, try typing it again.'
                        )
                        bic_message('black', '')
                    })
            } else {
                iban_message('black', 'Please enter your IBAN above.')
                bic_message('black', ' ')
                submit.disabled = true
            }
        }
        iban.addEventListener('keyup', onkey)

        bic.addEventListener('keyup', onkey)

        submit.addEventListener('click', (_) => {
            submit.disabled = true
            if (bic.value.length >= 8) {
                get('{{ route('api::verify_iban') }}', {
                    iban: iban.value,
                    bic: bic.value,
                })
                    .then((data) => {
                        if (data.status === true) {
                            form.submit()
                        } else {
                            update_iban_form(data)
                        }
                    })
                    .catch((err) => {
                        console.error(err) // Log the fetch error
                    })
            } else {
                bic_message('red', 'Please enter your BIC.')
            }
        })

        function iban_message(color, text) {
            ibanMessage.style.color = color
            ibanMessage.innerHTML = text
        }

        function bic_message(color, text) {
            bicMessage.style.color = color
            bicMessage.innerHTML = text
        }

        function update_iban_form(data) {
            if (data.status === false) {
                if (data.message.includes('BIC')) {
                    bic_message('red', data.message)
                    iban_message('green', 'Your IBAN is valid!')
                } else {
                    iban_message('red', data.message)
                    bic_message('red', '')
                }
                submit.disabled = true
            } else {
                iban_message('green', 'Your IBAN is valid!')
                bic_message(
                    'green',
                    'The BIC is also valid! Make sure that it matches your bank too!'
                )
                submit.disabled = false
            }
        }
    </script>
@endpush
