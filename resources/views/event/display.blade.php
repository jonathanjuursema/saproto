@extends('website.layouts.default-nobg')

@section('page-title')
    {{ $event->title }}
@endsection

@section('content')

    <div class="row">

        <div class="col-md-{{ ($event->activity && $event->activity->withParticipants() ? '8' : '8 col-md-offset-2') }}">

            @if($event->image)
                <img src="{{ $event->image->generateImagePath(800,300) }}"
                     style="width: 100%; margin-bottom: 30px; box-shadow: 0 0 20px -7px #000;">
            @endif

            <div class="panel panel-default">

                <div class="panel-heading" style="text-align: center;">

                    @if ($event->secret)
                        [ Hidden! ]
                    @endif

                    From {{ date('l j F Y, H:i', $event->start) }} till

                    @if (($event->end - $event->start) < 3600 * 24)
                        {{ date('H:i', $event->end) }}
                    @else
                        {{ date('l j F, H:i', $event->end) }}
                    @endif

                    @ {{ $event->location }}

                    @if(Auth::check() && Auth::user()->can('board'))
                        <a href="{{ route("event::edit", ['id'=>$event->id]) }}">
                            <span class="label label-success pull-right">Edit</span>
                        </a>
                    @endif

                </div>

                <div class="panel-body" id="event-description">

                    {!! Markdown::convertToHtml($event->description) !!}

                    @if($event->committee)

                        <hr>

                        This activity is brought to you by the
                        <a href="{{ route('committee::show', ['id' => $event->committee->id]) }}">
                            {{ $event->committee->name }}
                        </a>.

                    @endif

                </div>

            </div>

            @if($event->activity && Auth::check() && Auth::user()->member)

                @foreach($event->activity->helpingCommitteeInstances as $key => $instance)

                    @if($key % 2 == 1)

                        <div class="row">

                            @endif

                            <div class="col-md-6">

                                <div class="panel panel-default">

                                    <div class="panel-heading">

                                        {{ $instance->committee->name }}

                                    </div>

                                    <div class="panel-body">

                                        @if ($event->activity->helpingUsers($instance->id)->count() < 1)
                                            <p style="text-align: center;">
                                                No people are currently helping.
                                            </p>
                                        @endif

                                        @foreach($event->activity->helpingUsers($instance->id) as $participation)
                                            <div class="member">
                                                <div class="member-picture"
                                                     style="background-image:url('{!! ($participation->user->photo ? $participation->user->photo->generateImagePath(100, 100) : '') !!}');">
                                                </div>
                                                <a href="{{ route("user::profile", ['id'=>$participation->user->id]) }}">{{ $participation->user->name }}</a>

                                                @if(Auth::user()->can('board'))
                                                    <p class="pull-right activity__admin-controls">
                                                        <a class="activity__admin-controls__button--delete"
                                                           href="{{ route('event::deleteparticipation', ['participation_id' => $participation->id]) }}">
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                        </a>
                                                    </p>
                                                @endif

                                            </div>
                                        @endforeach

                                    </div>

                                    @if($instance->committee->isMember(Auth::user()) || Auth::user()->can('board'))

                                        <div class="panel-footer">

                                            @if($instance->committee->isMember(Auth::user()))

                                                @if($event->activity->getHelpingParticipation($instance->committee, Auth::user()) !== null)
                                                    <a class="btn btn-warning" style="width: 100%;"
                                                       href="{{ route('event::deleteparticipation', ['participation_id' => $event->activity->getHelpingParticipation($instance->committee, Auth::user())->id]) }}">
                                                        I won't help anymore.
                                                    </a>
                                                @elseif($instance->users->count() < $instance->amount)
                                                    <a class="btn btn-success" style="width: 100%;"
                                                       href="{{ route('event::addparticipation', ['id' => $event->id, 'helping_committee_id' => $instance->id]) }}">
                                                        I'll help!
                                                    </a>
                                                @endif

                                            @endif

                                            @if(Auth::user()->can('board'))
                                                <form class="form-horizontal"
                                                      action="{{ route("event::addparticipationfor", ['id' => $event->id, 'helping_committee_id' => $instance->id]) }}" method="post">

                                                    {{ csrf_field() }}

                                                    <div class="input-group">
                                                        <input type="text" class="form-control member-name"
                                                               placeholder="John Doe"
                                                               required>
                                                        <input type="hidden" class="member-id" name="user_id" required>
                                                        <span class="input-group-btn">
                                                    <button class="btn btn-danger member-clear" disabled>
                                                        <i class="fa fa-eraser" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                    </button>
                                                </span>
                                                    </div>

                                                </form>
                                            @endif

                                        </div>

                                    @endif

                                </div>

                            </div>

                            @if($key % 2 === 1)

                        </div>

                    @endif

                @endforeach

            @endif

        </div>

        <div class="col-md-4">

            @if($event->activity && Auth::check() && Auth::user()->member && $event->activity->withParticipants())

                <div class="panel panel-default">

                    <div class="panel-heading" style="text-align: center;">
                        Activity Sign-up
                        @if($event->activity->canSubscribe())
                            ({{ ($event->activity->freeSpots() == -1 ? 'unlimited' : $event->activity->freeSpots()) }}
                            places available)
                        @endif
                    </div>

                    <div class="panel-body" id="event-description">

                        @if ($event->activity->closed)
                            <p style="text-align: center;">
                                This activity is closed and cannot be changed anymore.
                            </p>
                        @endif

                        <p style="text-align: center;">
                            @if($event->activity->getParticipation(Auth::user()) !== null)
                                @if ($event->activity->getParticipation(Auth::user())->backup)
                                    You are on the <strong>back-up list</strong>.
                                @else
                                    <strong>You are signed up for this activity!</strong>
                                @endif
                            @else
                                You are <strong>not signed</strong> up for this activity.
                            @endif
                        </p>

                        <p>
                            @if($event->activity->getParticipation(Auth::user()) !== null)
                                @if($event->activity->canUnsubscribe() || $event->activity->getParticipation(Auth::user())->backup)
                                    <a class="btn btn-warning" style="width: 100%;"
                                       href="{{ route('event::deleteparticipation', ['participation_id' => $event->activity->getParticipation(Auth::user())->id]) }}">
                                        @if ($event->activity->getParticipation(Auth::user())->backup)
                                            Sign me out of the back-up list.
                                        @else
                                            Sign me out. <i class="fa fa-frown-o" aria-hidden="true"></i>
                                        @endif
                                    </a>
                                @endif
                            @else
                                @if($event->activity->canSubscribe() || !$event->activity->hasStarted())
                                    <a class="btn btn-{{ ($event->activity->isFull() ? 'warning' : 'success') }}"
                                       style="width: 100%;"
                                       href="{{ route('event::addparticipation', ['id' => $event->id]) }}">
                                        @if ($event->activity->isFull() || !$event->activity->canSubscribe())
                                            {{ ($event->activity->isFull() ? 'Activity is full.' : 'Sign-up closed.') }}
                                            Sign me up for the back-up list.
                                        @else
                                            Sign me up!
                                        @endif
                                    </a>
                                @endif
                            @endif
                        </p>

                        <p style="text-align: center;">
                            Sign up is possible between {{ date('F j, H:i', $event->activity->registration_start) }}
                            and {{ date('F j, H:i', $event->activity->registration_end) }}. You can sign out
                            until {{ date('F j, H:i', $event->activity->deregistration_end) }}.
                        </p>

                        <p style="text-align: center">
                            <strong>
                                Participation fee &euro;{{ number_format($event->activity->price, 2, '.', ',') }}
                            </strong>
                        </p>

                        <hr>

                        @if($event->activity->users->count() > 0)
                            <p style="text-align: center; padding-bottom: 5px;">
                                {{ $event->activity->users->count() }} participants:
                            </p>
                        @endif

                        @foreach($event->activity->users as $user)

                            <div class="member">
                                <div class="member-picture"
                                     style="background-image:url('{!! ($user->photo ? $user->photo->generateImagePath(100, 100) : '') !!}');"></div>
                                <a href="{{ route("user::profile", ['id'=>$user->id]) }}">{{ $user->name }}</a>

                                @if(Auth::user()->can('board') && !$event->activity->closed)
                                    <p class="pull-right activity__admin-controls">
                                        <a class="activity__admin-controls__button--delete"
                                           href="{{ route('event::deleteparticipation', ['participation_id' => $user->pivot->id]) }}">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </a>
                                    </p>
                                @endif
                            </div>

                        @endforeach

                        @if ($event->activity->backupUsers->count() > 0)

                            <hr>

                            <p style="text-align: center; padding-bottom: 5px;">
                                {{ $event->activity->backupUsers->count() }} people on back-up list:
                            </p>

                            @foreach($event->activity->backupUsers as $user)

                                <div class="member">
                                    <div class="member-picture"
                                         style="background-image:url('{!! ($user->photo ? $user->photo->generateImagePath(100, 100) : '') !!}');"></div>
                                    <a href="{{ route("user::profile", ['id'=>$user->id]) }}">{{ $user->name }}</a>

                                    @if(Auth::user()->can('board'))
                                        <p class="pull-right activity__admin-controls">
                                            <a class="activity__admin-controls__button--delete"
                                               href="{{ route('event::deleteparticipation', ['participation_id' => $user->pivot->id]) }}">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    @endif
                                </div>

                            @endforeach

                        @endif

                    </div>

                    @if(Auth::user()->can('board'))

                        <div class="panel-footer clearfix">
                            <div class="form-group">
                                <div id="user-select">
                                    <form class="form-horizontal"
                                          action="{{ route("event::addparticipationfor", ['id' => $event->id]) }}" method="post">

                                        {{ csrf_field() }}

                                        <div class="input-group">
                                            <input type="text" class="form-control member-name"
                                                   placeholder="John Doe"
                                                   required>
                                            <input type="hidden" class="member-id" name="user_id" required>
                                            <span class="input-group-btn">
                                                    <button class="btn btn-danger member-clear" disabled>
                                                        <i class="fa fa-eraser" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                    </button>
                                                </span>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                    @endif

                </div>

            @elseif($event->activity && $event->activity->withParticipants())

                <div class="panel panel-default">

                    <div class="panel-heading">
                        Activity Sign-up
                    </div>

                    <div class="panel-body" style="text-align: center;">
                        <p>This activity requires you to sign-up. You can only sign-up when you are a member.</p>
                        @if(!Auth::check()) <p>Please <a href="{{ route('login::show') }}">log-in</a> if you are already
                            a member.</p> @endif
                        @if(Auth::check() && !Auth::user()->member) <p>Please <a href="{{ route('becomeamember') }}">become
                                a member</a> to sign-up for this activity.</p> @endif
                    </div>

                </div>

            @endif

        </div>

    </div>

@endsection

@section('javascript')

    @parent

    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

    <script>

        $(".member-name").each(function() {
            $(this).autocomplete({
                minLength: 3,
                source: "{{ route("api::members") }}",
                select: function (event, ui) {
                    $(this).val(ui.item.name + " (ID: " + ui.item.id + ")").prop('disabled', true);
                    $(this).next(".member-id").val(ui.item.id);
                    $(this).parent().find(".member-clear").prop('disabled', false);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function (ul, item) {
                console.log(ul);
                return $("<li>").append(item.name).appendTo(ul);
            };
        });

        $(".member-clear").each(function() {
            $(this).click(function (e) {
                e.preventDefault();
                $(this).parent().parent().find(".member-name").val("").prop('disabled', false);
                $(this).prop('disabled', true);
                $("#member-id").val("");
            });
        });

    </script>

@endsection

@section('stylesheet')

    @parent

    <style>

        #event-description {
            text-align: justify;
        }

    </style>

@endsection