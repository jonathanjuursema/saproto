@extends('website.layouts.default-nobg')

@section('page-title')
    Calendar
@endsection

@section('content')

    <div class="row">

        @foreach($events as $key => $section)

            <div class="col-md-4">

                <div class="panel panel-default">

                    <div class="panel-body">

                        <h3 style="text-align: center;">
                            @if($key == 0)
                                Soon
                            @elseif($key == 1)
                                This month
                            @elseif($key == 2)
                                Later
                            @endif
                        </h3>

                        <hr>

                        @if(count($section) > 0)

                            <? $week = date('W', $section[0]->start); ?>

                            @foreach($section as $key => $event)

                                @if($week != date('W', $event->start))
                                    <hr>
                                @endif

                                <a class="activity"
                                   href="{{ route('event::show', ['id' => $event->id]) }}">
                                    <div class="activity {{ ($key % 2 == 1 ? 'odd' : '') }}" {!! ($event->secret ? 'style="opacity: 0.3;"' : '') !!}>
                                        <p><strong>{{ $event->title }}</strong></p>
                                        <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $event->location }}
                                        </p>
                                        <p>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            {{ date('l j F', $event->start) }}, {{ date('H:i', $event->start) }} -
                                            @if (($event->end - $event->start) < 3600 * 24)
                                                {{ date('H:i', $event->end) }}
                                            @else
                                                {{ date('j M, H:i', $event->end) }}
                                            @endif
                                        </p>
                                    </div>
                                </a>

                                <? $week = date('W', $event->start); ?>

                            @endforeach

                        @else
                            <p style="font-style: italic; text-align: center;">
                                No activities to show...
                            </p>
                        @endif

                    </div>

                </div>

            </div>

        @endforeach

    </div>

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-default">

                <div class="panel-body">

                    <span style="font-weight: 700; margin: 0 15px;">Archive</span>

                    @foreach($years as $year)

                        <span style="padding: 5px 15px; background-color: rgba(0,0,0,0.05); margin-right: 15px;">
                        <a href="{{ route('event::archive', ['year'=>$year]) }}" style="text-decoration: none;">
                            {{ $year }}
                        </a>
                        </span>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

@endsection

@section('stylesheet')

    @parent

    <style>

        a.activity, a.activity:hover, a.activity:active {
            color: #000;
            text-decoration: none;
        }

        div.activity {
            padding: 10px 20px;
            transition: all 0.2s;
        }

        div.activity.odd {
            background-color: rgba(0, 0, 0, 0.04);
        }

        div.activity:hover {
            transform: scale(1.05);
            color: #fff;
            background-color: #333;
        }

        div.activity p {
            margin: 0;
            margin: 5px 0;
        }

        div.activity .fa {
            width: 15px;
            text-align: center;
        }

    </style>

@endsection
