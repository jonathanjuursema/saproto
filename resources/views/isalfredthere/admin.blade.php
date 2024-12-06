@extends('website.layouts.redesign.dashboard')

@section('page-title')
    IsAlfredThere.nl
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-6 col-lg-4">

            <form method="post" action="{{ route("minisites::isalfredthere::update") }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body where_is_alfred">

                        <p>
                            @if($status->status == 'there')
                                You are there!
                            @elseif($status->status == 'away')
                                You'll be back at {{ $status->back }}.
                            @elseif($status->status == 'jur')
                                You do not seem to be Alfred. Do you happen to feel like a Jur today?
                            @else
                                Your whereabouts are currently not known.
                            @endif
                        </p>

                        <hr>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="where_is_alfred" id="where_is_alfred_1"
                                   value="there" required {{ $status->status == 'there' ? 'checked' : '' }}>
                            <label class="form-check-label" for="where_is_alfred_1">
                                I'm there!
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="where_is_alfred" id="where_is_jur"
                                   value="jur" required {{ $status->status == 'jur' ? 'checked' : '' }}>
                            <label class="form-check-label" for="where_is_jur">
                                I'm not Alfred, I'm Jur!
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="where_is_alfred" id="where_is_alfred_2"
                                   value="away" required {{ $status->status == 'away' ? 'checked' : '' }}>
                            <label class="form-check-label" for="where_is_alfred_2">
                                I'll be back in a while!
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="where_is_alfred" id="where_is_alfred_3"
                                   value="unknown" required {{ $status->status == 'unknown' ? 'checked' : '' }}>
                            <label class="form-check-label" for="where_is_alfred_3">
                                I'd like to reset my whereabouts!
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="where_is_alfred" id="where_is_alfred_4"
                                   value="text_only"
                                   required {{ ($status->status == 'unknown' && !empty($status->text)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="where_is_alfred_4">
                                I would only like to display a message!
                            </label>
                        </div>

                        @include('components.forms.datetimepicker',[
                            'name' => 'back',
                            'label' => "I'll be back around:",
                            'placeholder' => $status->status == 'away' ? $status->backunix : strtotime('now +1 hour'),
                            'form_class_name' => $status->status == 'away' ? '' : 'd-none'
                        ])

                        <div id="alfred-text"
                             class="{{($status->status == 'away'||($status->status=='unknown'&&!empty($status->text))) ? '' : 'd-none'}}">
                            <br>
                            <input name="is_alfred_there_text" type="text" class="form-control"
                                   placeholder="additional message" value="{{$status->text}}">
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-success float-end ms-3">Save!</button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const dateSelect = document.getElementById('datetimepicker-back-form');
        const dateBack = document.getElementById('datetimepicker-back');
        const alfredText = document.getElementById('alfred-text');

        const radioList = Array.from(document.querySelectorAll('.where_is_alfred input[type="radio"]'));
        radioList.forEach(el => {
            el.addEventListener('change', _ => {

                if (el.checked && el.value === 'away') {
                    dateSelect.classList.remove('d-none');
                    alfredText.classList.remove('d-none');
                    alfredText.querySelector('input').placeholder = 'Additional message';
                    dateBack.required = true;
                } else if (el.checked && el.value === 'text_only') {
                    alfredText.classList.remove('d-none');
                    dateSelect.classList.add('d-none');
                    alfredText.querySelector('input').placeholder = 'Message!';
                    alfredText.required = true;
                } else {
                    dateSelect.classList.add('d-none');
                    alfredText.classList.add('d-none');
                    dateBack.required = false;
                }
            });
        });
    </script>

@endsection
