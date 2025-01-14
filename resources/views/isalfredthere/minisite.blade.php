@extends('website.layouts.redesign.generic-nonavandfooter')

@section('page-title')
    Is Alfred There?
@endsection

@push('head')
    <meta http-equiv="refresh" content="86400">
@endpush

@section('container')

    <div class="row text-white">

        <div class="col-md-12 text-center">

            <h1 class="mt-3 mb-3" style="font-size: 70px;">Is Alfred There?</h1>

            <h1 id="alfred-status"
                class="mt-3 mb-3 proto-countdown"
                style="font-size: 50px;"
                data-countdown-text-counting="Nope. Alfred will be back in {}."
                data-countdown-text-finished="Alfred should be there. 👀">
                We're currently looking for Alfred, please stand by...
            </h1>
            <h4 id="alfred-actualtime"></h4>
            <h1 id="alfred-text"></h1>
            <div class="mt-5 mb-5 flex flex-row">
                <i class="d-none" id="alfred-error" style="font-size: 120px;"><i
                        class="fas fa-triangle-exclamation"></i>
                </i> <i class="d-none" id="alfred-there" style="font-size: 120px;"><i
                        class="far fa-smile-beam"></i>
                </i> <i class="d-none" id="jur-there" style="font-size: 120px;"><i
                        class="far fa-face-grin-squint"></i>
                </i> <i class="d-none" id="alfred-away" style="font-size: 120px;"><i
                        class="far fa-grimace"></i>
                </i> <i class="d-none" id="alfred-unknown" style="font-size: 120px;"><i
                        class="fas fa-circle-question"></i>
                </i>
            </div>
            <a href="//{{ config('app-proto.primary-domain') }}{{ route('homepage', [], false) }}">
                <img src="{{ asset('images/logo/inverse.png') }}" alt="Proto logo" width="472px">
            </a>

        </div>

    </div>

@endsection

@push('stylesheet')

    <style rel="stylesheet">
        body {
            background-color: var(--bs-warning);
        }

        main {
            border: none !important;
        }
    </style>

@endpush

@push('javascript')

    @vite('resources/assets/js/echo.js')

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const statusElement = document.getElementById('alfred-status');
        const text = document.getElementById('alfred-text');
        const time = document.getElementById('alfred-actualtime');
        const statuses = {
            there: {
                text: 'Alfred is there!',
                htmlElement: document.getElementById('alfred-there'),
                color: 'bg-success'
            },
            jur: {
                text: 'Jur is here to help you! <br> <div style="font-size: 20px;">You might have to check Flex Office though...</div>',
                htmlElement: document.getElementById('jur-there'),
                color: 'bg-success'
            },
            unknown: {
                text: 'We couldn\'t find Alfred...',
                htmlElement: document.getElementById('alfred-unknown'),
                color: 'bg-warning'
            },
            text: {
                text: 'We couldn\'t find Alfred...',
                htmlElement: document.getElementById('alfred-unknown'),
                color: 'bg-warning'
            },
            away: {
                text: 'Nope, Alfred will be back in a bit.',
                htmlElement: document.getElementById('alfred-away'),
                color: 'bg-danger'
            },
            error: {
                text: 'We couldn\'t find Alfred...',
                htmlElement: document.getElementById('alfred-error'),
                color: 'bg-warning'
            }
        };

        window.addEventListener('load', _ => {
            updateStatus({
                status: "{{$status}}",
                text: "{{$text}}",
                unix: "{{$unix}}"
            });

            window.Echo.channel(`isalfredthere`)
                .listen('IsAlfredThereEvent', (status) => {
                    updateStatus(status);
                })
                .error((error) => {
                    console.error(error);
                    setTimeout(() => {
                        window.location.reload();
                    }, 10000);
                });
        });

        const updateStatus = (status) => {
            if (status.text?.length > 0) {
                text.innerHTML = '"'.concat(status.text ?? '', '"');
            } else {
                text.innerHTML = '';
            }

            // hide all smileys
            Object.keys(statuses).forEach((key) => {
                statuses[key].htmlElement.classList.add('d-none');
            });

            // set the new status
            setNewStatus(statuses[status.status]);

            // set the time submessage and start the timer if Alfred is away
            if (status.status === 'away') {
                const date = new window.moment(status.unix);
                time.innerHTML = `That would be ${date.format('DD-MM-Y HH:mm')}.`;
                time.classList.remove('d-none');
                statusElement.setAttribute('data-countdown-start', date.unix());
                window.timerList.forEach((timer) => {
                    timer.start();
                });
            }
        };

        const setNewStatus = (newStatus) => {
            // stop all timers
            window.timerList.forEach((timer) => {
                timer.stop();
            });
            // set the big status text
            statusElement.innerHTML = newStatus.text;

            //reveal the correct smiley
            newStatus.htmlElement.classList.remove('d-none');

            // hide the time submessage
            time.classList.add('d-none');

            document.body.classList.remove('bg-success', 'bg-warning', 'bg-danger');
            // set the correct color corresponding to the status
            document.body.classList.add(newStatus.color);
        };
    </script>
@endpush
