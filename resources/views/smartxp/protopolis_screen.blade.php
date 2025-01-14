@extends('website.master')

@section('page-title')
    Protopolis Screen v12
@endsection

@push('stylesheet')
    <style>
        html {
            width: 100vw;
            height: 100vh;
        }

        body {
            height: 100%;
            background-color: #f1f1f1;
            padding: 30px;
        }

        span {
            display: block;
        }

        #info-row {
            height: 20%;
        }

        .protubebackground {
            background-color: rgb(255 255 255);
            opacity: 0.8;
            width: 100%;
        }

        .protubecard {
            border-radius: 8px;
            padding: 0;
            overflow: hidden;
            border: 0px solid #00aac0;
            border-left-width: 4px;
        }

        .font-size-lg {
            font-size: 1.5rem;
        }

        .activity.past {
            opacity: 0.5;
        }

        span.current {
            font-weight: bold;
        }

        .scroll-title {
            animation: slide-left 15s linear infinite;
        }

        @keyframes slide-left {
            0% {
                -webkit-transform: translateX(0);
                transform: translateX(0);
            }
            100% {
                -webkit-transform: translateX(-50%);
                transform: translateX(-50%);
            }
        }
    </style>
@endpush

@section('body')
    <div class="row text-black">
        {{-- narrowcasting --}}
        <div class="col-9 protubecard">
            @include('narrowcasting.display')
        </div>

        <div class="ms-2 col h-auto d-flex flex-column">
            {{-- On screen clock --}}
            <div class="row mb-3">
                <div
                    class="protubecard protubebackground p-3 text-center font-weight-bold display-3"
                >
                    <div id="clock" class="notice">Loading</div>
                </div>
            </div>

            <div class="row flex-grow-1">
                <div class="protubecard protubebackground p-3">
                    <div class="box-header font-size-lg">
                        <i class="fa-solid fa-calendar-days fa-fw me-1"></i>
                        Timetable
                    </div>

                    <div id="timetable" class="notice">
                        Loading timetable...
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="info-row" class="row text-black d-flex">
        <div class="col-9 mt-3 h-100" style="padding: 0">
            <div id="activities" class="d-flex flex-row mb-3 gap-2 h-100">
                <div
                    class="notice protubecard protubebackground flex-grow-1 p-2 font-weight-bold"
                >
                    <div id="event-loader" class="font-size-lg">
                        Loading events
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 mt-3 h-100" style="padding: 0 0 0 8px">
            <div
                id="protopeners"
                class="box protubecard protubebackground p-3 h-100"
            >
                <div class="box-header font-size-lg">
                    <i
                        class="fas fa-door-closed fa-fw me-1"
                        id="protopolis-fa"
                    ></i>
                    Protopolis
                </div>

                <div id="protopeners-timetable" class="h-100"></div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        function updateTimetable() {
            const timetable = document.getElementById('timetable')
            timetable.innerHTML = ''
            get('{{ route('api::screen::timetable') }}')
                .then((data) => {
                    if (data.length > 0) {
                        let count = 0
                        data.forEach((timetableItem) => {
                            if (count >= 3) return
                            if (!timetableItem.over) {
                                let start = moment.unix(timetableItem.start)
                                let end = moment.unix(timetableItem.end)
                                let time =
                                    start.format('HH:mm') +
                                    ' - ' +
                                    end.format('HH:mm')
                                let title = timetableItem.title

                                let activityDiv = document.createElement('div')
                                activityDiv.className = 'activity'

                                if (timetableItem.studyShort) {
                                    let yearSpan =
                                        document.createElement('span')
                                    yearSpan.className = 'float-end ms-2'
                                    yearSpan.innerHTML =
                                        '<i class="fas fa-graduation-cap fa-fw me-2"></i>' +
                                        timetableItem.studyShort +
                                        ' ' +
                                        (timetableItem.year
                                            ? 'Year ' + timetableItem.year
                                            : '')
                                    activityDiv.appendChild(yearSpan)
                                }

                                let titleStrong =
                                    document.createElement('strong')
                                titleStrong.innerHTML = timetableItem.type
                                activityDiv.appendChild(titleStrong)
                                activityDiv.innerHTML += '<br>'

                                let titleSpan = document.createElement('span')
                                titleSpan.className = timetableItem.current
                                    ? 'current'
                                    : ''
                                titleSpan.innerHTML = title
                                activityDiv.appendChild(titleSpan)

                                let locationDiv = document.createElement('div')
                                locationDiv.className = 'w-100 h-10'
                                locationDiv.innerHTML +=
                                    '<i class="fas fa-clock fa-fw me-1"></i>' +
                                    time

                                let placeSpan = document.createElement('span')
                                placeSpan.className = 'float-end'
                                placeSpan.innerHTML =
                                    '<i class="fas fa-map-marker-alt fa-fw me-1"></i>' +
                                    timetableItem.place
                                locationDiv.appendChild(placeSpan)
                                activityDiv.appendChild(locationDiv)
                                activityDiv.innerHTML += '<br>'
                                activityDiv.innerHTML += '<hr>'

                                timetable.appendChild(activityDiv)
                                count++
                            }
                        })
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
            get('{{ route('api::events::upcoming', ['limit' => 4]) }}')
                .then((data) => {
                    if (data.length > 0) {
                        document.getElementById('activities').innerHTML = ''
                        data.forEach((activity) => {
                            let start = moment.unix(activity.start)
                            let end = moment.unix(activity.end)
                            let time
                            if (start.format('DD-MM') === end.format('DD-MM')) {
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
                            let newDiv = document.createElement('div')
                            newDiv.className =
                                'activity bg-img protubecard protubebackground flex-grow-1' +
                                (activity.over ? 'past' : '')
                            newDiv.style.padding = '15px'
                            if (activity.image) {
                                newDiv.style.background =
                                    'linear-gradient(rgba(255,255,255,0.7), rgba(255,255,255,0.7)), url(' +
                                    activity.image +
                                    ')'
                                newDiv.style.backgroundSize = 'cover'
                                newDiv.style.backgroundPosition =
                                    'center center'
                                newDiv.style.backgroundRepeat = 'no-repeat'
                            }

                            let titleDiv = document.createElement('div')
                            titleDiv.style.overflowX = 'hidden'
                            titleDiv.style.display = 'block'
                            titleDiv.className = 'mx-2'

                            let titleH3 = document.createElement('h3')
                            titleH3.style.whiteSpace = 'nowrap'
                            titleH3.style.width = 'fit-content'

                            let titleSpan = document.createElement('span')
                            titleSpan.innerHTML = activity.title
                            titleSpan.className = 'me-5'
                            titleSpan.style.display = 'inline-block'
                            titleH3.appendChild(titleSpan)

                            titleDiv.appendChild(titleH3)
                            let timeDiv = document.createElement('div')
                            timeDiv.innerHTML =
                                '<i class="fas fa-clock fa-fw me-1"></i>' + time

                            let locationSpan = document.createElement('div')
                            locationSpan.innerHTML =
                                '<i class="fas fa-map-marker-alt fa-fw me-1"></i>' +
                                activity.location +
                                '</span>'

                            newDiv.appendChild(titleDiv)
                            newDiv.appendChild(timeDiv)
                            newDiv.appendChild(locationSpan)
                            document
                                .getElementById('activities')
                                .appendChild(newDiv)
                        })

                        document
                            .getElementById('activities')
                            .childNodes.forEach((activity) => {
                                let div = activity.childNodes[0]
                                let H3 = div.childNodes[0]
                                if (H3.clientWidth > div.clientWidth) {
                                    H3.classList.add('scroll-title')
                                    H3.appendChild(
                                        H3.childNodes[0].cloneNode(true)
                                    )
                                }
                            })
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

        function updateProtopeners() {
            const timetable = document.getElementById('protopeners-timetable')
            const protopolisFa = document.getElementById('protopolis-fa')

            get('{{ route('api::screen::timetable::protopeners') }}')
                .then((data) => {
                    if (data.length > 0) {
                        document.getElementById(
                            'protopeners-timetable'
                        ).innerHTML = ''
                        let open = false,
                            count = 0
                        data.forEach((protOpener) => {
                            if (protOpener.over || count > 1) return
                            else if (protOpener.current) open = true
                            let start = moment.unix(protOpener.start)
                            let end = moment.unix(protOpener.end)
                            let time =
                                start.format('HH:mm') +
                                ' - ' +
                                end.format('HH:mm')

                            let newDiv = document.createElement('div')
                            newDiv.className =
                                'activity ' +
                                (protOpener.current ? 'current' : '')

                            let timeDiv = document.createElement('div')
                            timeDiv.className = 'float-start h-100'
                            timeDiv.innerHTML = time

                            let titleDiv = document.createElement('div')
                            titleDiv.className = 'float-end h-100'
                            titleDiv.innerHTML =
                                '<strong>' + protOpener.title + '</strong>'

                            newDiv.appendChild(timeDiv)
                            newDiv.appendChild(titleDiv)

                            let protOpenDiv = document.getElementById(
                                'protopeners-timetable'
                            )
                            protOpenDiv.appendChild(newDiv)
                            protOpenDiv.appendChild(
                                document.createElement('br')
                            )
                            protOpenDiv.appendChild(
                                document.createElement('hr')
                            )

                            count++
                        })
                        if (open) {
                            protopolisFa.classList.replace(
                                'fa-door-closed',
                                'fa-door-open'
                            )
                        } else {
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

        function updateClock() {
            document.getElementById('clock').innerHTML =
                moment().format('HH:mm:ss')
        }

        window.addEventListener('load', (_) => {
            updateTimetable()
            updateActivities()
            updateProtopeners()
            updateClock()

            const everySecond = 1000
            const everyMinute = 60 * 1000
            const everyFiveMinutes = 5 * 60 * 1000
            setInterval(updateTimetable, everyFiveMinutes)
            setInterval(updateActivities, everyFiveMinutes)
            setInterval(updateProtopeners, everyMinute)
            setInterval(updateClock, everySecond)
        })
    </script>
@endpush
