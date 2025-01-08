<form method="post"
      action="{{ ($email == null ? route("email::store") : route("email::update", ['id' => $email->id])) }}"
      enctype="multipart/form-data">

    @csrf

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            @yield('page-title')
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="description">Internal description:</label>
                        <input type="text" class="form-control" id="description" name="description"
                               placeholder="A short description that only the board can see."
                               value="{{ $email->description ?? '' }}" required>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="subject">E-mail subject:</label>
                        <input type="text" class="form-control" id="subject" name="subject"
                               placeholder="The e-mail subject."
                               value="{{ $email->subject ?? '' }}" required>
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="sender_name">Sender name:</label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name"
                               placeholder="{{ Auth::user()->name }}"
                               value="{{ $email->sender_name ?? Auth::user()->name }}" required>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="sender_address">Sender e-mail:</label>
                        <div class="input-group mb-3">
                            <input name="sender_address" type="text" class="form-control" placeholder="board"
                                   value="{{ $email->sender_address ?? '' }}" required>
                            <span class="input-group-text"
                                  id="basic-addon2">@ {{ Config::string('proto.emaildomain') }}</span>
                        </div>
                    </div>

                </div>

            </div>

            <div class="form-group">
                <label for="editor">E-mail</label>
                @include('components.forms.markdownfield', [
                    'name' => 'body',
                    'placeholder' => 'Text goes here.',
                    'value' => $email ? $email->body : null
                ])
            </div>

            <div class="row">


                <div class="col-md-6">

                    <div class="form-group">
                        <label>Recipients:</label>

                        @include('components.forms.checkbox', [
                            'type' => 'radio',
                            'id' => 'destination_type_all_members',
                            'name' => 'destinationType',
                            'checked' => $email?->to_member,
                            'required' => true,
                            'value' => 'members',
                            'label' => 'All members'
                        ])

                        @include('components.forms.checkbox', [
                            'type' => 'radio',
                            'id' => 'destination_type_active_members',
                            'name' => 'destinationType',
                            'checked' => $email?->to_active,
                            'required' => true,
                            'value' => 'active',
                            'label' => 'All active members'
                        ])

                        @include('components.forms.checkbox', [
                            'type' => 'radio',
                            'id' => 'destination_type_pending_members',
                            'name' => 'destinationType',
                            'checked' => $email?->to_pending,
                            'required' => true,
                            'value' => 'pending',
                            'label' => 'All pending members'
                        ])

                        @include('components.forms.checkbox', [
                            'type' => 'radio',
                            'id' => 'destination_type_event',
                            'name' => 'destinationType',
                            'checked' => $email?->to_event,
                            'required' => true,
                            'value' => 'event',
                            'label' => 'These events:'
                        ])

                        @if($email?->to_event)
                            <strong>Current selection</strong>

                            <ul class="list-group">
                                @foreach($email->events as $event)
                                    <li class="list-group-item">
                                        {{ $event->title }} ({{ $event->formatted_date->simple }})
                                    </li>
                                @endforeach
                            </ul>

                            <strong>Replace selection</strong>
                        @endif

                        <div class="form-group">
                            <div class="form-group autocomplete">
                                <input class="form-control event-search" id="eventSelect" name="eventSelect[]"
                                       @disabled(!$email?->to_event) multiple>
                            </div>
                        </div>

                        <div class="form-group {{ ($email?->to_event ?: 'd-none') }} mt-1 mb-2" id="backupDiv">
                            @include('components.forms.checkbox', [
                                'name' => 'toBackup',
                                'checked' => $email?->to_backup,
                                'label' => 'Send to backup users'
                            ])
                            <em>
                                <b>Note:</b>
                                Specify in your e-mail that the recipient is not automatically enrolled in the activity!
                            </em>
                        </div>

                        @include('components.forms.checkbox', [
                            'type' => 'radio',
                            'id' => 'destination_type_lists',
                            'name' => 'destinationType',
                            'checked' => $email?->to_list && $email?->to_backup,
                            'value' => 'lists',
                            'label' => 'These e-mail lists:'
                        ])

                        <select multiple name="listSelect[]" id="listSelect" class="form-control"
                                {{ ($email?->to_list ? '' : 'disabled="disabled"') }}>

                            @foreach(App\Models\EmailList::all() as $list)

                                <option value="{{ $list->id }}" @selected($email?->hasRecipientList($list))>
                                    {{ $list->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="col-md-6">
                    @include('components.forms.datetimepicker', [
                        'name' => 'time',
                        'label' => 'Scheduled:',
                        'placeholder' => $email ? $email->time : strtotime(Carbon::now()->endOfDay())
                    ])
                </div>

            </div>

        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-success float-end">Save</button>

            <a href="{{ route("email::index") }}" class="btn btn-default">Cancel</a>

        </div>

    </div>

</form>

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const eventSelect = document.getElementById('eventSelect');
        const listSelect = document.getElementById('listSelect');
        const destinationSelectList = Array.from(document.getElementsByName('destinationType'));
        const backupToggle = document.getElementById('backupDiv');
        const toggleList = {
            'event': [false, true, false],
            'members': [true, true, true],
            'active': [true, true, true],
            'pending': [true, true, true],
            'lists': [true, false, true],
        };

        destinationSelectList.forEach(el => {
            el.addEventListener('click', e => {
                const toggle = toggleList[el.value];
                eventSelect.disabled = toggle[0];
                listSelect.disabled = toggle[1];

                if (toggle[2]) backupToggle.classList.add('d-none');
                else backupToggle.classList.remove('d-none');
            });
        });
    </script>

@endpush
