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
                        {{--                        select component with all EmailDestination cases from email->destination--}}
                        <div class="form-group">
                            <label for="destination">Recipients:</label>
                            <select name="destination" id="destination" class="form-select">
                                <option
                                    value="{{\App\Enums\EmailDestination::NO_DESTINATION}}" @selected(empty($email->destination))>
                                    Choose a destination
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::ALL_MEMBERS}}" @selected($email?->destination == \App\Enums\EmailDestination::ALL_MEMBERS)>
                                    All Members
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::PENDING_MEMBERS}}" @selected($email?->destination == \App\Enums\EmailDestination::PENDING_MEMBERS)>
                                    Pending members
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::ACTIVE_MEMBERS}}" @selected($email?->destination == \App\Enums\EmailDestination::ACTIVE_MEMBERS)>
                                    Active members
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::EVENT}}" @selected($email?->destination == \App\Enums\EmailDestination::EVENT)>
                                    Event participants
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::EVENT_WITH_BACKUP}}" @selected($email?->destination == \App\Enums\EmailDestination::EVENT_WITH_BACKUP)>
                                    Events participants with backup users
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::EMAIL_LISTS}}" @selected($email?->destination == \App\Enums\EmailDestination::EMAIL_LISTS)>
                                    Email lists
                                </option>
                                <option
                                    value="{{\App\Enums\EmailDestination::SPECIFIC_USERS}}" @selected($email?->destination == \App\Enums\EmailDestination::SPECIFIC_USERS)>
                                    Specific users
                                </option>
                            </select>

                            @if($email?->destination == \App\Enums\EmailDestination::EVENT_WITH_BACKUP||$email?->destination == \App\Enums\EmailDestination::EVENT)
                                <strong>Current selection of events</strong>

                                <ul class="list-group">
                                    @foreach($email->events as $event)
                                        <li class="list-group-item">
                                            {{ $event->title }} ({{ $event->formatted_date->simple }})
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div id="eventGroup"
                                 class="{{ (($email?->destination!==\App\Enums\EmailDestination::EVENT && $email?->destination!==\App\Enums\EmailDestination::EVENT_WITH_BACKUP) ? 'd-none' : '') }}">
                                <strong>Replace event selection</strong>
                                <div class="form-group autocomplete" id="eventGroup">
                                    <label for="eventSelect">Event(s):</label>
                                    <input class="form-control event-search" id="eventSelect" name="eventSelect[]"
                                           multiple>
                                </div>
                            </div>


                            @if($email?->destination == \App\Enums\EmailDestination::SPECIFIC_USERS)
                                <strong>Current selection of users</strong>

                                <ul class="list-group">
                                    @php /** @var App\Models\User $user */ @endphp

                                    @foreach($email->specificUsers()->get() as $user)
                                        <li class="list-group-item">
                                            {{ $user->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div id="userGroup"
                                 class="{{ ($email?->destination!==\App\Enums\EmailDestination::SPECIFIC_USERS ? 'd-none' : '') }}">
                                <strong>Replace user selection</strong>
                                <div class="form-group autocomplete">
                                    <label for="user">User(s):</label>
                                    <input class="form-control user-search" id="users" name="users[]"
                                           data-label="User(s):"
                                           multiple>
                                </div>
                            </div>

                            <div id="emailListGroup"
                                 class="{{ ($email?->destination!==\App\Enums\EmailDestination::EMAIL_LISTS ? 'd-none' : '') }}">
                                <label for="listSelect">Email list(s):</label>
                                <select multiple name="listSelect[]" id="listSelect" class="form-control">

                                    @foreach(App\Models\EmailList::all() as $list)

                                        <option value="{{ $list->id }}" @selected($email?->hasRecipientList($list))>
                                            {{ $list->name }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

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
        const selectList = document.getElementById('destination');
        const eventGroup = document.getElementById('eventGroup');
        const userGroup = document.getElementById('userGroup');
        const emailListSelect = document.getElementById('emailListGroup');

        selectList.addEventListener('change', function () {
            if (selectList.value === '{{ \App\Enums\EmailDestination::EVENT }}' || selectList.value === '{{ \App\Enums\EmailDestination::EVENT_WITH_BACKUP }}') {
                eventGroup.classList.remove('d-none');
            } else {
                eventGroup.classList.add('d-none');
            }

            if (selectList.value === '{{ \App\Enums\EmailDestination::SPECIFIC_USERS }}') {
                userGroup.classList.remove('d-none');
            } else {
                userGroup.classList.add('d-none');
            }

            if (selectList.value === '{{ \App\Enums\EmailDestination::EMAIL_LISTS }}') {
                emailListSelect.classList.remove('d-none');
            } else {
                emailListSelect.classList.add('d-none');
            }
        });
    </script>

@endpush
