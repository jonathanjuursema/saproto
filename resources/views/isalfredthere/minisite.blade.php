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
                </i> <i class="" id="alfred-unknown" style="font-size: 120px;"><i
                        class="fas fa-circle-question"></i>
                </i>
            </div>
            <a href="//{{ config('app-proto.primary-domain') }}{{ route('homepage', [], false) }}">
                <img src="{{ asset('images/logo/inverse.png') }}" alt="Proto logo" width="472px" height="120px">
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
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const status = document.getElementById('alfred-status');
        const text = document.getElementById('alfred-text');
        const time = document.getElementById('alfred-actualtime');
        let oldStatus = null;
        const statuses = {
            there: {
                text: 'Alfred is there!',
                htmlElement: document.getElementById('alfred-there'),
                color: 'bg-success',
            },
            jur: {
                text: 'Jur is here to help you! <br> <div style="font-size: 20px;">You might have to check Flex Office though...</div>',
                htmlElement: document.getElementById('jur-there'),
                color: 'bg-success',
            },
            unknown: {
                text: 'We couldn\'t find Alfred...',
                htmlElement: document.getElementById('alfred-unknown'),
                color: 'bg-warning',
            },
            away: {
                text: 'Nope, Alfred will be back in a bit.',
                htmlElement: document.getElementById('alfred-away'),
                color: 'bg-danger',
            },
            error: {
                text: 'We couldn\'t find Alfred...',
                htmlElement: document.getElementById('alfred-error'),
                color: 'bg-warning',
            },
        };

        window.addEventListener('load', _ => {
            lookForAlfred();
            setInterval(lookForAlfred, 10000);
        });

        function lookForAlfred() {
            get("{{route('api::isalfredthere')}}")
                .then(data => {
                    //set the extra text Alfred can set himself
                    if (data.text.length > 0) {
                        text.innerHTML = '"'.concat(data.text, '"');
                    } else {
                        text.innerHTML = '';
                    }

                    // keep track if the status has changed, if not do nothing
                    if (oldStatus === data.status) {
                        return;
                    }
                    oldStatus = data.status;

                    // hide all smileys
                    Object.keys(statuses).forEach((key) => {
                        statuses[key].htmlElement.classList.add('d-none');
                    });

                    // set the new status
                    setNewStatus(statuses[data.status]);

                    // set the time submessage and start the timer if Alfred is away
                    if (data.status === 'away') {
                        time.innerHTML = `That would be ${data.back}.`;
                        time.classList.remove('d-none');
                        status.setAttribute('data-countdown-start', data.backunix);
                        window.timerList.forEach((timer) => {
                            timer.start();
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                    setNewStatus(statuses.error);
                });
        }

        const setNewStatus = (newStatus) => {
            // stop all timers
            window.timerList.forEach((timer) => {
                timer.stop();
            });
            // set the big status text
            status.innerHTML = newStatus.text;

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
