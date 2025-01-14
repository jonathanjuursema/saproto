<div id="archive-bar" class="row justify-content-center px-2">
    <div class="col-12 col-sm-auto text-center mb-2 overflow-auto">
        <div class="btn-group mb-1">
            @if (Route::currentRouteName() == 'event::index')
                <span class="bg-primary text-white px-3 py-2 rounded-start">
                    Upcoming
                </span>
                <span class="bg-secondary text-white px-3 py-2">Archive</span>
            @else
                <a
                    href="{{ route('event::index', ['category' => $cur_category]) }}"
                    class="btn btn-secondary"
                >
                    Upcoming
                </a>
                <span class="bg-primary text-white px-3 py-2">Archive</span>
            @endif

            @foreach ($years as $y)
                <a
                    href="{{ route('event::archive', ['year' => $y, 'category' => $cur_category]) }}"
                    class="btn btn-{{ Route::currentRouteName() == 'event::archive' && $y == $year ? 'primary' : 'light' }} {{ $loop->index == count($years) - 1 ? 'rounded-end' : '' }}"
                >
                    {{ $y }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="col-12 col-sm-auto mb-2 text-center">
        <div class="btn-group">
            <button
                type="button"
                class="btn btn-info px-4 px-sm-3 {{ ! Auth::check() || ! Auth::user()->can('board') ? 'rounded-end' : '' }}"
                data-bs-toggle="modal"
                data-bs-target="#calendar-modal"
            >
                <i class="fas fa-calendar-alt"></i>
                <span class="d-none d-sm-inline-block ms-2">
                    Import Calendar
                </span>
            </button>

            @can('board')
                <a
                    href="{{ route('event::create') }}"
                    class="btn btn-info rounded-end"
                >
                    <i class="fas fa-calendar-plus me-2"></i>
                    <span class="d-none d-sm-inline-block ms-2">
                        Create Event
                    </span>
                </a>
            @endcan

            @php($categories = \App\Models\EventCategory::all())
            @if (count($categories) > 0)
                <form
                    class="form-inline ms-3"
                    action="{{ Route::currentRouteName() == 'event::archive' ? route('event::archive', ['year' => $year]) : route('event::index') }}"
                >
                    <div id="category-search" class="input-group">
                        <div class="input-group-prepend">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <select
                            id="category"
                            name="category"
                            class="form-control"
                        >
                            <option value="" @selected(! $cur_category)>
                                All
                            </option>
                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected($cur_category == $category)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<div id="calendar-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import our calendar into yours!</h4>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <p>
                    If you want to, you can import our calendar into yours. This
                    can be easily done by going to your favorite calendar
                    application and looking for an option similar to
                    <i>Import calendar by URL</i>
                    . You can then to copy the URL below.

                    <input
                        id="ical-url"
                        class="form-control"
                        value="{{ Auth::check() ? Auth::user()->getIcalUrl() : route('ical::calendar') }}"
                        readonly
                    />
                    <script nonce="{{ csp_nonce() }}">
                        document
                            .getElementById('ical-url')
                            .addEventListener('click', (e) => {
                                e.target.focus()
                                e.target.select()
                            })
                    </script>
                </p>

                <hr />

                <a
                    class="btn btn-info btn-block"
                    type="button"
                    target="_blank"
                    href="https://calendar.google.com/calendar/r?cid={{ urlencode(str_replace('https://', 'http://', Auth::check() ? Auth::user()->getIcalUrl() : route('ical::calendar'))) }}"
                >
                    <i class="fas fa-google me-2" aria-hidden="true"></i>
                    Add to Google Calendar
                </a>

                @if (Auth::check())
                    <hr />

                    <p class="text-center">
                        @if (Auth::user()->getCalendarRelevantSetting())
                            <strong>
                                Your are currently only syncing relevant events.
                            </strong>
                        @else
                                You are currently syncing all events.
                        @endif

                        <a
                            class="btn btn-{{ Auth::check() && Auth::user()->getCalendarRelevantSetting() ? 'success' : 'danger' }} btn-block mt-2"
                            type="button"
                            href="{{ route('event::toggle_relevant_only') }}"
                        >
                            @if (Auth::user()->getCalendarRelevantSetting())
                                Sync all my events.
                            @else
                                    Sync only relevant events.
                            @endif
                        </a>

                        <sub>
                            Relevant events are events you either attend,
                            organize or help with.
                        </sub>
                    </p>

                    <hr />

                    <p class="text-center">
                        <sub>
                            @if (Auth::user()->getCalendarAlarm())
                                You are currently receiving a reminder
                                {{ Auth::user()->getCalendarAlarm() }} hours
                                before an activity you participate in.
                            @else
                                You are currently
                                <strong>not</strong>
                                receiving a reminder before an activity you
                                participate in.
                            @endif
                        </sub>
                    </p>

                    <form
                        method="post"
                        action="{{ route('event::set_reminder') }}"
                    >
                        @csrf

                        <div class="row">
                            <div
                                class="col-6 col-sm-4 d-flex {{ Auth::user()->getCalendarAlarm() ? '' : 'offset-sm-2' }}"
                            >
                                <input
                                    id="hours"
                                    class="form-control"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.5"
                                    name="hours"
                                    min="0"
                                    value="{{ Auth::user()->getCalendarAlarm() ? Auth::user()->getCalendarAlarm() : '' }}"
                                />
                                <label for="hours" class="form-label ms-2 mt-2">
                                    hours
                                </label>
                            </div>
                            <div class="col-6 col-sm-4">
                                <button
                                    class="btn btn-success btn-block"
                                    type="submit"
                                    name="save"
                                >
                                    Set reminder.
                                </button>
                            </div>
                            @if (Auth::user()->getCalendarAlarm())
                                <div class="col-sm-4">
                                    <button
                                        class="btn btn-danger btn-block"
                                        type="submit"
                                        name="delete"
                                    >
                                        Remove reminder.
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>

                    <p class="text-center">
                        <sub>
                            Reminders are not supported in Google Calendar.
                            Blame Google. 😟
                        </sub>
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
