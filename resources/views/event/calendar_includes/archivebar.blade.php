<div class="row justify-content-center">

    <div class="col-12 col-sm-auto text-center mb-2" style="overflow-x: auto;">

        <div class="btn-group mb-1">

            @if (Route::currentRouteName() == 'event::list')
                <span class="bg-primary text-white px-3 py-2 rounded-left">Upcoming</span>
                <span class="bg-secondary text-white px-3 py-2">Archive</span>
            @else
                <a href="{{ route('event::list', ['category' => $cur_category]) }}" class="btn btn-secondary">
                    Upcoming
                </a>
                <span class="bg-primary text-white px-3 py-2">Archive</span>
            @endif

            @foreach($years as $y)
                @if(Proto\Models\Event::countEventsPerYear($y) > 0)
                    <a href="{{ route('event::archive', ['year'=>$y, 'category' => $cur_category]) }}"
                       class="btn btn-{{ Route::currentRouteName() == 'event::archive' && $y == $year ? 'primary' : 'light' }}
                        {{ $loop->index == count($years)-1 ? 'rounded-right' : '' }}">
                        {{ $y }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
    <div class="col-12 col-sm-auto mb-2 text-center">
        <div class="btn-group">
            <button type="button" class="btn btn-info px-4 px-sm-3 {{ !Auth::check() || !Auth::user()->can('board') ? 'rounded-right' : '' }}" data-toggle="modal" data-target="#calendar-modal">
                <i class="fas fa-calendar-alt"></i><span class="d-none d-sm-inline-block ml-2">Import Calendar</span>
            </button>

            @if(Auth::check() && Auth::user()->can('board'))
                <a href="{{ route("event::add") }}" class="btn btn-info px-4 px-sm-3 rounded-right">
                    <i class="fas fa-calendar-plus"></i><span class="d-none d-sm-inline-block ml-2">Create Event</span>
                </a>
            @endif

            @php($categories = \Proto\Models\EventCategory::all())
            @if(count($categories) > 0)
                <form class="form-inline ml-3" action="{{ Route::currentRouteName() == 'event::archive' ? route('event::archive', ['year' => $year]) : route('event::list')}}">
                    <div class="input-group" style="max-width: 250px">
                        <div class="input-group-prepend">
                            <button type="submit" class="btn btn-info"><i class="fas fa-search"></i></button>
                        </div>
                        <select id="category" name="category" class="form-control">
                            <option value="" {{ !$cur_category ? 'selected' : '' }}>
                                All
                            </option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $cur_category && $cur_category == $category ? 'selected' : '' }}>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Import our calendar into yours!</h4>
            </div>
            <div class="modal-body">

                <p>
                    If you want to, you can import our calendar into yours. This can be easily done by going to your
                    favorite calendar application and looking for an option similar to <i>Import calendar by URL</i>.
                    You can then to copy the URL below.
                </p>
                <p>
                    <input class="form-control" onclick="this.select()"
                           value="{{ Auth::check() ? Auth::user()->getIcalUrl() : route("ical::calendar") }}">
                </p>

                <hr>

                <a class="btn btn-info" type="button" style="width: 100%;" target="_blank"
                   href="https://calendar.google.com/calendar/r?cid={{ urlencode(str_replace("https://", "http://", Auth::check() ? Auth::user()->getIcalUrl() : route("ical::calendar"))) }}">
                    <i class="fas fa-google" aria-hidden="true"></i>
                    &nbsp;&nbsp;&nbsp;Add to Google Calendar
                </a>

                @if(Auth::check())

                    <hr>

                    <p style="text-align: center;">
                        @if (Auth::user()->getCalendarRelevantSetting())
                            <strong>Your are currently only syncing relevant events.</strong>
                        @else
                            You are currently syncing all events.
                        @endif

                        <a class="btn btn-{{ Auth::check() && Auth::user()->getCalendarRelevantSetting() ? 'success':'danger' }}"
                           type="button"
                           style="width: 100%;" href="{{ route('event::toggle_relevant_only') }}">
                            @if (Auth::user()->getCalendarRelevantSetting())
                                Sync all my events.
                            @else
                                Sync only relevant events.
                            @endif
                        </a>

                        <sub>
                            Relevant events are events you either attend, organize or help with.
                        </sub>

                    </p>

                    <hr>

                    <p style="text-align: center;">
                        <sub>
                            @if (Auth::user()->getCalendarAlarm())
                                You are currently recieving a reminder {{ Auth::user()->getCalendarAlarm() }} hours
                                before an
                                activity you participate in.
                            @else
                                You are currently <strong>not</strong> receiving a reminder before an activity you
                                participate in.
                            @endif
                        </sub>
                    </p>

                    <form method="post"
                          action="{{ route('event::set_reminder') }}">

                        {!! csrf_field() !!}

                        <div class="row">

                            <div class="{{ Auth::user()->getCalendarAlarm() ? 'col-md-4' : 'col-md-4 col-md-offset-2' }}">
                                <div class="input-group">
                                    <input class="form-control" type="number" step="0.01" placeholder="0.5"
                                           name="hours"
                                           value="{{ Auth::user()->getCalendarAlarm() ? Auth::user()->getCalendarAlarm() : '' }}">
                                    <div class="input-group-addon">hours</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success" type="submit" name="save" style="width: 100%;">
                                    Set reminder.
                                </button>
                            </div>
                            @if (Auth::user()->getCalendarAlarm())
                                <div class="col-md-4">
                                    <button class="btn btn-danger" type="submit" name="delete" style="width: 100%;">
                                        Remove reminder.
                                    </button>
                                </div>
                            @endif

                        </div>

                    </form>

                    <p style="text-align: center;">
                        <sub>
                            Reminders are not supported in Google Calendar. Blame Google. 😟
                        </sub>
                    </p>

                @endif

            </div>
        </div>
    </div>
</div>
