<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta
            name="viewport"
            content="initial-scale=1, maximum-scale=1, user-scalable=no"
        />
        <meta http-equiv="refresh" content="3600" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <link
            rel="shortcut icon"
            href="{{ asset('images/favicons/favicon' . mt_rand(1, 4) . '.png') }}"
        />

        <title>SmartXP Screen v3</title>

        @include('website.assets.stylesheets')

        <style>
            .h-33 {
                height: 33.33%;
            }

            .h-66 {
                height: 66.66%;
            }

            .green {
                color: #c1ff00;
            }

            html,
            body,
            #container {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                padding: 15px 10px;
                margin: 0;
                background-color: #333;
                overflow: hidden;
            }

            .box {
                position: relative;
                background-color: rgba(0, 0, 0, 0.5);
                border-bottom: 5px solid #c1ff00;
                box-shadow: 0 0 20px -7px #000;
                border-radius: 0.25rem;
                overflow: hidden;
            }

            .box-header {
                font-size: 30px;
                font-weight: bold;
                color: #fff;
                text-align: center;
                padding: 15px 0;
                margin: 0 40px;
                border-bottom: 2px solid #fff;
            }

            .box-header.small {
                font-size: 20px;
                margin: 0 10px;
            }

            .box-partial {
                padding: 20px 0;
            }

            .box-partial:nth-child(1) {
                padding-top: 0;
            }

            .box-partial:nth-last-child(1) {
                padding-bottom: 0;
            }

            .notice {
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                margin-top: 20px;
                color: #fff;
            }

            #time {
                height: 70px;
                line-height: 50px;
                padding: 5px 0;
                margin: 0 40px;
                font-weight: bold;
                border-bottom: 0;
                color: #fff;
                font-size: 35px;
                overflow: visible;
            }

            #ticker {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 5px;
                width: 100%;
                border-radius: 0.25rem;
                background-color: #c1ff00;
            }

            .activity:nth-child(even) {
                background-color: rgba(255, 255, 255, 0.05);
            }

            .activity.past {
                opacity: 0.5;
            }

            .activity.current {
                color: #c1ff00;
            }

            span.current {
                color: #c1ff00;
                font-weight: bold;
            }

            .activity {
                width: 100%;
                color: #fff;
                text-align: left;
                padding: 20px 40px;
                font-size: 20px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .busentry:nth-child(1) {
                padding-top: 10px;
            }

            .busentry:nth-child(even) {
                background-color: rgba(255, 255, 255, 0.1);
            }

            .busentry {
                padding: 5px 10px 5px 10px;
                color: #fff;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #protube {
                position: relative;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                border-bottom: none;
            }

            #protube.inactive {
                background-image: url({{ Config::string('app-proto.fishcam-url') }}) !important;
            }

            #protube-title {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                text-shadow: 0 0 5px #000;
                border: none;
            }

            #protube-ticker {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 5px;
                width: 100%;
                background-color: #c1ff00;
            }
        </style>
    </head>

    <body>
        <div id="container" class="row row-eq-height h-100">
            <div class="col-md-4">
                <div class="box-partial h-100">
                    <div class="box h-100">
                        <div class="box-header">
                            <i class="fas fa-calendar-alt fa-fw me-2"></i>
                            Timetable
                        </div>

                        <div id="timetable">
                            <div class="notice">Loading timetable...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="box-partial text-center h-33">
                    <div id="time" class="box">
                        <div id="clock">NOW!</div>
                        <div id="ticker"></div>
                    </div>

                    <br />

                    <img
                        src="{{ asset('images/logo/inverse.png') }}"
                        class="h-75"
                    />
                </div>

                <div class="box-partial h-33">
                    <div id="protube" class="box inactive h-100">
                        <div id="protube-title" class="box-header">
                            Connecting to ProTube...
                        </div>

                        <div id="protube-ticker"></div>
                    </div>
                </div>

                <div class="box-partial h-33">
                    <div id="protopeners" class="box h-100">
                        <div class="box-header small">
                            <i
                                class="fas fa-door-closed fa-fw me-2"
                                id="protopolis-fa"
                            ></i>
                            Protopolis
                        </div>

                        <div id="protopeners-timetable"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="box-partial h-66">
                    <div class="box h-100">
                        <div class="box-header">
                            <i class="fas fa-calendar-alt fa-fw me-2"></i>
                            Activities
                        </div>

                        <div id="activities">
                            <div class="notice">Loading activities...</div>
                        </div>
                    </div>
                </div>

                <div class="box-partial h-33">
                    <div class="box h-100">
                        <div class="row px-4">
                            <div class="col-md-6">
                                <div class="box-header small">
                                    <i class="fas fa-bus fa-fw me-1"></i>
                                    Westerbegraafplaats
                                </div>

                                <div id="businfo-wester" class="businfo"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="box-header small">
                                    <i class="fas fa-bus fa-fw me-1"></i>
                                    Langenkampweg
                                </div>

                                <div id="businfo-langen" class="businfo"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('website.assets.javascripts')
        @stack('javascript')

        <script type="text/javascript" nonce="{{ csp_nonce() }}">
            function updateClock() {
                let now = moment()
                document.getElementById('clock').innerHTML =
                    '<i class="fas fa-clock fa-fw me-2"></i>' +
                    now.format('HH:mm:ss')
                document.getElementById('ticker').style.width =
                    (now.format('s.SSS') / 60) * 100 + '%'
            }

            function updateTimetable() {
                const timetable = document.getElementById('timetable')

                get('{{ route('api::screen::timetable') }}')
                    .then((data) => {
                        if (data.length > 0) {
                            timetable.innerHTML = ''
                            let count = 0
                            for (let i in data) {
                                if (count >= 4) return
                                if (!data[i].over) {
                                    let start = moment.unix(data[i].start)
                                    let end = moment.unix(data[i].end)
                                    let time =
                                        start.format('HH:mm') +
                                        ' - ' +
                                        end.format('HH:mm')
                                    let title = data[i].title
                                    let displayTime =
                                        '<i class="fas fa-clock fa-fw me-1"></i>' +
                                        time +
                                        ' <span class="float-end"><i class="fas fa-map-marker-alt fa-fw me-1"></i>' +
                                        data[i].place +
                                        '</span>'
                                    timetable.innerHTML +=
                                        '<div class="activity">' +
                                        (data[i].studyShort
                                            ? '<span class="float-end ms-2">' +
                                              '<i class="fas fa-graduation-cap fa-fw me-2"></i>' +
                                              data[i].studyShort +
                                              ' ' +
                                              (data[i].year
                                                  ? 'Year ' + data[i].year
                                                  : '') +
                                              '</span> '
                                            : null) +
                                        '<strong>' +
                                        data[i].type +
                                        '</strong><br>' +
                                        '<span class="' +
                                        (data[i].current ? 'current' : '') +
                                        '">' +
                                        title +
                                        '</span><br>' +
                                        displayTime +
                                        '</div>'
                                    count++
                                }
                            }
                            if (count === 0) {
                                timetable.innerHTML =
                                    '<div class="notice">No more lectures today!</div>'
                            }
                        } else {
                            timetable.innerHTML =
                                '<div class="notice">No lectures today!</div>'
                        }
                    })
                    .catch((err) => {
                        console.error(err)
                        timetable.innerHTML =
                            '<div class="notice">Lectures could not be found...</div>'
                    })
            }

            function updateActivities() {
                get('{{ route('api::events::upcoming', ['limit' => 3]) }}')
                    .then((data) => {
                        if (data.length > 0) {
                            document.getElementById('activities').innerHTML = ''
                            for (let i in data) {
                                let start = moment.unix(data[i].start)
                                let end = moment.unix(data[i].end)
                                let time
                                if (
                                    start.format('DD-MM') ===
                                    end.format('DD-MM')
                                ) {
                                    time =
                                        start.format('DD-MM, HH:mm') +
                                        ' - ' +
                                        end.format('HH:mm')
                                } else {
                                    time =
                                        start.format('DD-MM, HH:mm') +
                                        ' - ' +
                                        end.format('DD-MM, HH:mm')
                                }
                                document.getElementById(
                                    'activities'
                                ).innerHTML +=
                                    '<div class="activity ' +
                                    (data[i].current
                                        ? 'current'
                                        : data[i].over
                                          ? 'past'
                                          : '') +
                                    '"><strong>' +
                                    data[i].title +
                                    '</strong><br><i class="fas fa-clock fa-fw me-1"></i> ' +
                                    time +
                                    ' <span class="float-end"><i class="fas fa-map-marker-alt fa-fw me-1"></i> ' +
                                    data[i].location +
                                    '</span></div>'
                            }
                        } else {
                            document.getElementById('activities').innerHTML =
                                '<div class="notice">No upcoming activities!</div>'
                        }
                    })
                    .catch((err) => {
                        console.error(err)
                        document.getElementById('activities').innerHTML =
                            '<div class="notice">Something went wrong during retrieval...</div>'
                    })
            }

            function updateBuses() {
                updateBus(43110270, 43110270, 'businfo-langen')
                updateBus(43005640, 43005630, 'businfo-wester')
            }

            function updateBus(stop, stop_other_side, id) {
                const element = document.getElementById(id)
                get('{{ urldecode(route('api::screen::bus')) }}', {
                    tpc_id: stop,
                    tpc_id_other: stop_other_side,
                })
                    .then((data) => {
                        let combinedBusses = {}
                        for (const [key, value] of Object.entries(data)) {
                            Object.assign(combinedBusses, value.Passes)
                        }
                        if (Object.keys(combinedBusses).length > 0) {
                            element.innerHTML = ''
                            Object.entries(combinedBusses).sort(
                                function (a, b) {
                                    return (
                                        new Date(
                                            a[1].ExpectedArrivalTime
                                        ).valueOf() -
                                        new Date(
                                            b[1].ExpectedArrivalTime
                                        ).valueOf()
                                    )
                                }
                            )

                            for (const [key, value] of Object.entries(
                                combinedBusses
                            ).slice(0, 4)) {
                                let colorLate =
                                    (Math.abs(
                                        new Date(value.ExpectedArrivalTime) -
                                            new Date(value.TargetArrivalTime)
                                    ) /
                                        1000) *
                                        60 >
                                    1
                                        ? '#ff0000'
                                        : '#c1ff00'
                                let drivingColor =
                                    value.TripStopStatus === 'DRIVING'
                                        ? '#c1ff00'
                                        : '#fff'
                                if (value.TripStopStatus !== 'ARRIVED') {
                                    element.innerHTML +=
                                        '<div class="busentry">' +
                                        `<span style=color:${colorLate}>` +
                                        new Date(value.ExpectedArrivalTime)
                                            .toISOString()
                                            .substr(11, 8)
                                            .substr(0, 5) +
                                        '</span>' +
                                        ' ' +
                                        value.TransportType +
                                        ' ' +
                                        value.LinePublicNumber +
                                        ` ` +
                                        `<span style="color: ${drivingColor};">` +
                                        value.TripStopStatus +
                                        '</span><br>Towards ' +
                                        value.DestinationName50 +
                                        '</div>'
                                }
                            }
                        } else {
                            element.innerHTML =
                                '<div class="notice">No buses!</div>'
                        }
                    })
                    .catch((err) => {
                        console.error(err)
                        element.innerHTML =
                            '<div class="notice">Something went wrong during retrieval...</div>'
                    })
            }

            function updateProtopeners() {
                const timetable = document.getElementById(
                    'protopeners-timetable'
                )
                const protopolisFa = document.getElementById('protopolis-fa')

                get('{{ route('api::screen::timetable::protopeners') }}')
                    .then((data) => {
                        if (data.length > 0) {
                            document.getElementById(
                                'protopeners-timetable'
                            ).innerHTML = ''
                            let open = false,
                                count = 0
                            for (let i in data) {
                                if (data[i].over) continue
                                else if (data[i].current) open = true
                                let start = moment.unix(data[i].start)
                                let end = moment.unix(data[i].end)
                                let time =
                                    start.format('HH:mm') +
                                    ' - ' +
                                    end.format('HH:mm')
                                document.getElementById(
                                    'protopeners-timetable'
                                ).innerHTML +=
                                    '<div class="activity ' +
                                    (data[i].current ? 'current' : '') +
                                    '">' +
                                    '   <div class="float-start">' +
                                    time +
                                    '</div>' +
                                    '   <div class="float-end"><strong>' +
                                    data[i].title +
                                    '</strong></div>' +
                                    '</div>'
                                count++
                            }
                            if (open) {
                                protopolisFa.classList.add('green')
                                protopolisFa.classList.replace(
                                    'fa-door-closed',
                                    'fa-door-open'
                                )
                            } else {
                                protopolisFa.classList.remove('green')
                                protopolisFa.classList.replace(
                                    'fa-door-open',
                                    'fa-door-closed'
                                )
                            }
                            if (count === 0)
                                timetable.innerHTML =
                                    '<div class="notice">Protopolis closed for today!</div>'
                        } else {
                            timetable.innerHTML =
                                '<div class="notice">Protopolis closed today!</div>'
                        }
                    })
                    .catch((err) => {
                        console.error(err)
                        timetable.innerHTML =
                            '<div class="notice">Something went wrong during retrieval...</div>'
                    })
            }

            window.addEventListener('load', (_) => {
                updateTimetable()
                updateActivities()
                updateProtopeners()
                updateClock()
                updateBuses()
                updateProtopeners()

                const everySecond = 1000
                const everyMinute = 60 * 1000
                const everyFiveMinutes = 5 * 60 * 1000
                setInterval(updateTimetable, everyFiveMinutes)
                setInterval(updateActivities, everyFiveMinutes)
                setInterval(updateProtopeners, everyMinute)
                setInterval(updateClock, everySecond)
                setInterval(updateBuses, everyFiveMinutes)
            })
        </script>
    </body>
</html>
