<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta
            name="viewport"
            content="initial-scale=1, maximum-scale=1, user-scalable=no"
        />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <meta name="theme-color" content="#C1FF00" />

        <meta property="og:type" content="website" />
        <meta
            property="og:title"
            content="Ticket Scanner for {{ $event->title }}"
        />

        <link
            rel="shortcut icon"
            href="{{ asset('images/favicons/favicon' . mt_rand(1, 4) . '.png') }}"
        />

        <title>Ticket Scanner for {{ $event->title }}</title>

        @include('website.assets.stylesheets')

        <style type="text/css">
            * {
                box-sizing: border-box;
            }

            html {
                background-color: #555;
                font-family: Lato, sans-serif;
            }

            body {
                padding: 20px;
                background-color: transparent;
            }

            .title {
                margin-top: 30px;
                font-size: 30px;
                color: #fff;
                text-align: center;
            }

            #scanner-field,
            #feedback-field {
                margin-top: 30px;
                padding-top: 20px;
                padding-bottom: 20px;

                font-size: 30px;
                width: 100%;

                border: none;

                text-align: center;
                background-color: transparent;
                color: #fff;

                display: block;

                outline: none !important;
            }

            .blink {
                animation: blink 2s linear infinite;
            }

            @keyframes blink {
                50% {
                    opacity: 0;
                }
            }

            .fade-out-50 {
                animation: fade-out-50 1s linear forwards;
            }

            @keyframes fade-out-50 {
                from {
                    opacity: 0.5;
                }
                to {
                    opacity: 0;
                }
            }

            #flash {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #555;
                display: none;
            }

            #video {
                position: absolute;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                opacity: 0.5;
                z-index: -1;
            }

            #video video,
            canvas {
                width: 100%;
                height: auto;
            }

            #video video.drawingBuffer,
            canvas.drawingBuffer {
                display: none;
            }

            .history th,
            .history td {
                color: #fff !important;
            }

            .history tr:first-child {
                font-weight: bold;
            }

            .history tr:nth-child(even) {
                background-color: rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>

    <body>
        <div id="video"></div>
        <div class="container-fluid">
            <p class="title">Ticket Scanner for {{ $event->title }}</p>

            <p id="feedback-field" class="blink">Searching barcode...</p>

            <hr />

            <table class="table history">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Ticket</th>
                        <th>Scanned</th>
                        <th>Valid</th>
                    </tr>
                </thead>
                <tbody id="history"></tbody>
            </table>
        </div>

        <div id="flash"></div>

        @include('website.assets.javascripts')

        <script type="text/javascript" nonce="{{ csp_nonce() }}">
            const feedbackField = document.getElementById('feedback-field')
            let prevRead = ''

            function initializeCamera() {
                Quagga.init(
                    {
                        inputStream: {
                            name: 'Live',
                            type: 'LiveStream',
                            constraints: {
                                facingMode: 'environment',
                            },
                            target: document.getElementById('video'),
                        },
                        decoder: {
                            readers: ['code_39_reader'],
                            multiple: false,
                        },
                    },
                    (err) => {
                        if (err) return console.error(err)
                        Quagga.start()
                    }
                )

                Quagga.onDetected((data) => {
                    const code = data.codeResult.code
                    if (code !== prevRead) {
                        scan(code)
                        prevRead = code
                    }
                })
            }

            function setStatus(status) {
                switch (status) {
                    case 'received':
                        feedbackField.innerHTML = 'Validating barcode...'
                        break
                    case 'error':
                        feedbackField.classList.remove('blink')
                        feedbackField.innerHTML =
                            'Something went wrong. Try again!'
                        flash('danger')
                        setTimeout(setStatus, 1000)
                        break
                    case 'ok':
                        feedbackField.classList.remove('blink')
                        feedbackField.innerHTML = 'Valid ticket!'
                        flash('primary')
                        setTimeout(setStatus, 1000)
                        break
                    default:
                        feedbackField.classList.add('blink')
                        feedbackField.innerHTML = 'Searching for barcode...'
                        break
                }
            }

            const flash = (color) => {
                document.getElementById('flash').className =
                    'bg-' + color + ' opacity-0 fade-out-50'
            }

            function scan(barcode) {
                if (barcode === '') return
                setStatus('received')
                get('{{ route('api::scan', ['event' => $event->id]) }}', {
                    barcode: barcode,
                })
                    .then((res) => parseReply(res.data, res.message, res.code))
                    .catch((err) => {
                        console.error(err)
                        setStatus('error')
                    })
            }

            function parseReply(data, message, code) {
                switch (code) {
                    case 500:
                        flash('danger')
                        feedbackField.classList.remove('blink')
                        feedbackField.innerHTML = message
                        setTimeout(setStatus, 1000)
                        break
                    case 403:
                        flash('warning')
                        feedbackField.classList.remove('blink')
                        feedbackField.innerHTML = message
                        setTimeout(setStatus, 1000)
                        document
                            .getElementById('history')
                            .prepend(
                                createTicketEl(
                                    data,
                                    false,
                                    'Used on ' + data.scanned
                                )
                            )
                        break
                    case 200:
                        setStatus('ok')
                        document
                            .getElementById('history')
                            .prepend(createTicketEl(data, true, 'Valid'))
                        break
                }
            }

            function createTicketEl(data, valid, message) {
                let el = document.createElement('tr')
                el.innerHTML = `<td>${data.id}</td>
            <td>${data.user.name}</td>
            <td>${data.ticket.product.name}</td>
            <td>${timeNow()}</td>
            <td><span class='text-${valid ? 'success' : 'warning'}'>${message}</span></td>`
                return el
            }

            function timeNow() {
                const d = new Date()
                return (
                    ('0' + d.getHours()).slice(-2) +
                    ':' +
                    ('0' + d.getMinutes()).slice(-2) +
                    ':' +
                    ('0' + d.getSeconds()).slice(-2)
                )
            }

            window.addEventListener('load', (_) => {
                initializeCamera()
            })
        </script>
    </body>
</html>
