@extends('website.layouts.redesign.dashboard')

@section('page-title')
    IsAlfredThere.nl
@endsection

@php
    use App\Enums\IsAlfredThereEnum;
    use Carbon\Carbon;
@endphp

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <form
                method="post"
                action="{{ route('minisites::isalfredthere::update') }}"
            >
                @csrf

                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body where_is_alfred">
                        <p>
                            @if ($status == IsAlfredThereEnum::THERE->value)
                                You are there!
                            @elseif ($status == IsAlfredThereEnum::AWAY->value)
                                You'll be back at
                                {{ Carbon::parse($unix)->format('Y-m-d H:i') }}.
                            @elseif ($status == IsAlfredThereEnum::JUR->value)
                                You do not seem to be Alfred. Do you happen to feel like a Jur today?
                            @else
                                    Your whereabouts are currently not known.
                            @endif
                        </p>

                        <hr />

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="where_is_alfred"
                                id="where_is_alfred_1"
                                value="there"
                                required
                                {{ $status == IsAlfredThereEnum::THERE->value ? 'checked' : '' }}
                            />
                            <label
                                class="form-check-label"
                                for="where_is_alfred_1"
                            >
                                I'm there!
                            </label>
                        </div>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="where_is_alfred"
                                id="where_is_jur"
                                value="jur"
                                required
                                {{ $status == IsAlfredThereEnum::JUR->value ? 'checked' : '' }}
                            />
                            <label class="form-check-label" for="where_is_jur">
                                I'm not Alfred, I'm Jur!
                            </label>
                        </div>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="where_is_alfred"
                                id="where_is_alfred_2"
                                value="away"
                                required
                                {{ $status == IsAlfredThereEnum::AWAY->value ? 'checked' : '' }}
                            />
                            <label
                                class="form-check-label"
                                for="where_is_alfred_2"
                            >
                                I'll be back in a while!
                            </label>
                        </div>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="where_is_alfred"
                                id="where_is_alfred_3"
                                value="unknown"
                                required
                                {{ $status == IsAlfredThereEnum::UNKNOWN->value ? 'checked' : '' }}
                            />
                            <label
                                class="form-check-label"
                                for="where_is_alfred_3"
                            >
                                I'd like to reset my whereabouts!
                            </label>
                        </div>

                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="where_is_alfred"
                                id="where_is_alfred_4"
                                value="text"
                                required
                                {{ $status == IsAlfredThereEnum::TEXT_ONLY->value ? 'checked' : '' }}
                            />
                            <label
                                class="form-check-label"
                                for="where_is_alfred_4"
                            >
                                I would only like to display a message!
                            </label>
                        </div>

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'back',
                                'label' => "I'll be back around:",
                                'placeholder' =>
                                    $status == IsAlfredThereEnum::AWAY->value
                                        ? Carbon::parse($unix)->timestamp
                                        : strtotime('now +1 hour'),
                                'form_class_name' =>
                                    $status == IsAlfredThereEnum::AWAY->value ? '' : 'd-none',
                            ]
                        )

                        <div
                            id="alfred-text"
                            class="{{ $status == IsAlfredThereEnum::AWAY->value || $status == IsAlfredThereEnum::TEXT_ONLY->value ? '' : 'd-none' }}"
                        >
                            <br />
                            <input
                                name="is_alfred_there_text"
                                type="text"
                                class="form-control"
                                placeholder="additional message"
                                value="{{ $text }}"
                            />
                        </div>
                    </div>

                    <div class="card-footer">
                        <button
                            type="submit"
                            class="btn btn-success float-end ms-3"
                        >
                            Save!
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const dateSelect = document.getElementById('datetimepicker-back-form')
        const dateBack = document.getElementById('datetimepicker-back')
        const alfredText = document.getElementById('alfred-text')

        const radioList = Array.from(
            document.querySelectorAll('.where_is_alfred input[type="radio"]')
        )
        radioList.forEach((el) => {
            el.addEventListener('change', (_) => {
                if (
                    el.checked &&
                    el.value === '{{ IsAlfredThereEnum::AWAY }}'
                ) {
                    dateSelect.classList.remove('d-none')
                    alfredText.classList.remove('d-none')
                    alfredText.querySelector('input').placeholder =
                        'Additional message'
                    dateBack.required = true
                } else if (el.checked && el.value === 'text') {
                    alfredText.classList.remove('d-none')
                    dateSelect.classList.add('d-none')
                    alfredText.querySelector('input').placeholder = 'Message!'
                    alfredText.required = true
                } else {
                    dateSelect.classList.add('d-none')
                    alfredText.classList.add('d-none')
                    dateBack.required = false
                }
            })
        })
    </script>
@endsection
