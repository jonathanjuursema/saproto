@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Activity Overview between {{ date('Y-m-d', $start) }} and {{ date('Y-m-d', $end) }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-12">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">

                    <form method="get">

                        <div class="row">

                            <div class="col-md-3">

                                <div class="form-group">
                                    @include('website.layouts.macros.datetimepicker',[
                                        'name' => 'start',
                                        'format' => 'date'
                                    ])
                                    <label for="signup_start">Query start</label>
                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="form-group">
                                    @include('website.layouts.macros.datetimepicker',[
                                        'name' => 'end',
                                        'format' => 'date'
                                    ])
                                    <label for="signup_start">Query end</label>
                                </div>

                            </div>

                            <div class="col-md-3">

                                <input type="submit" class="btn btn-success btn-block" value="Get me some activities!">

                            </div>

                        </div>

                    </form>

                </div>

                <div class="table-responsive">
                <table class="table table-sm table-hover">

                    <thead>

                    <tr class="bg-dark text-white">

                        <td>Start</td>
                        <td>Event</td>
                        <td>Organizing Committee</td>
                        <td>Participants</td>
                        <td>Helpers</td>
                        <td>Present</td>

                    </tr>

                    </thead>

                    @foreach($events as $event)

                        <tr>

                            <td>{{ date('Y-m-d H:i', $event->start) }}</td>
                            <td>
                                <a href="{{ route('event::show', ['id' => $event->getPublicId()]) }}">
                                    {{ $event->title }}
                                </a>
                            </td>
                            <td>
                                @if ($event->committee)
                                    <a href="{{ route('committee::show', ['id' => $event->committee->slug]) }}">
                                        {{ $event->committee->name }}
                                    </a>
                                @else
                                    <span class="font-italic text-muted">not set</span>
                                @endif
                            </td>

                            @if ($event->activity)

                                <td>{{ $event->activity->users->count() }}</td>
                                <td>
                                    @if($event->activity->helpingCommitteeInstances->count() > 0)
                                        @foreach($event->activity->helpingCommitteeInstances as $helping_committee)
                                            <a href="{{ route('committee::show', ['id' => $helping_committee->committee->slug]) }}">
                                                {{ $helping_committee->committee->name }}
                                            </a>: {{ $helping_committee->getHelpingCount() }}<br>
                                        @endforeach
                                    @else
                                        <span class="font-italic text-muted">not set</span>
                                    @endif
                                </td>
                                <td>{{ $event->activity->presentUsers->count() }}</td>

                            @else
                                <td><span class="font-italic text-muted">no activity</span></td>
                                <td><span class="font-italic text-muted">no activity</span></td>
                                <td><span class="font-italic text-muted">no activity</span></td>
                            @endif

                        </tr>

                    @endforeach

                </table>
                </div>

            </div>

        </div>

    </div>

@endsection