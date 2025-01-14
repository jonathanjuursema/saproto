<div class="card">
    <div class="card-header bg-dark text-white">
        People helping this activity
    </div>

    <ul class="list-group list-group-flush">
        @foreach ($event->activity->helpingCommitteeInstances as $key => $instance)
            <li class="list-group-item">
                <p class="card-title">
                    <strong>
                        {{ $instance->committee->name }}
                        @if ($instance->committee->isMember(Auth::user()) || Auth::user()->can('board'))
                                ({{ $instance->users->count() }}/{{ $instance->amount }})
                        @endif
                    </strong>

                    @if ($event->activity->helpingUsers($instance->id)->count() < 1)
                        <p class="card-text">
                            No people are currently helping.
                        </p>
                    @else
                        @include(
                            'event.display_includes.render_participant_list',
                            [
                                'participants' => $event->activity->helpingUsers($instance->id),
                                'event' => $event,
                            ]
                        )
                    @endif

                    @if (! $event->activity->closed && $instance->committee->isMember(Auth::user()))
                        @if ($event->activity->getHelpingParticipation($instance->committee, Auth::user()) !== null)
                            <a
                                class="btn btn-outline-warning btn-block mt-1"
                                href="{{ route('event::deleteparticipation', ['participation_id' => $event->activity->getHelpingParticipation($instance->committee, Auth::user())->id]) }}"
                            >
                                I won't help anymore.
                            </a>
                        @elseif ($instance->users->count() < $instance->amount)
                            <a
                                class="btn btn-outline-success btn-block mt-1"
                                href="{{ route('event::addparticipation', ['id' => $event->id, 'helping_committee_id' => $instance->id]) }}"
                            >
                                I'll help!
                            </a>
                        @endif
                    @endif

                    @if (Auth::user()->can('board') && ! $event->activity->closed)
                        <form
                            class="form-horizontal mt-2"
                            method="post"
                            action="{{ route('event::addparticipationfor', ['id' => $event->id, 'helping_committee_id' => $instance->id]) }}"
                        >
                            {{ csrf_field() }}

                            <div class="row mb-3">
                                <div class="col-9">
                                    <div class="form-group autocomplete">
                                        <input
                                            class="form-control user-search"
                                            name="user_id"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="col-3">
                                    <button
                                        class="btn btn-outline-primary btn-block"
                                        type="submit"
                                    >
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </p>
            </li>
        @endforeach
    </ul>
</div>
