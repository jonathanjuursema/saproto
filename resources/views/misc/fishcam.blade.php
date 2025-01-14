@extends('website.layouts.redesign.generic')

@section('page-title')
    The FishCam&trade;
@endsection

@section('container')
    <div class="row justify-content-center" style="height: 75vh">
        <div class="col-12 h-100">
            <div id="fishcam-warning" class="card mb-3">
                <div class="card-header bg-danger text-white">
                    <i
                        class="fas fa-exclamation-triangle me-2"
                        aria-hidden="true"
                    ></i>
                    Warning | Please don't load this stream over a mobile
                    connection.
                </div>

                <div class="card-body">
                    <p class="card-text">
                        The fishcam stream is of very high quality, and streams
                        with roughly 0.5 MB/s. If you have a 1 gigabyte data
                        plan, you will use it up completely over the course of
                        half an hour.
                    </p>
                </div>

                <div class="card-footer">
                    <button
                        href="#"
                        id="fishcam-activate"
                        class="btn btn-warning btn-block"
                    >
                        <i class="fas fa-fish mr-2"></i>
                        I understand, start the fishcam anyway!
                    </button>
                </div>
            </div>

            <div id="fishcam-unavailable" class="card mb-3 d-none">
                <div class="card-header bg-danger text-white fishcam-warning">
                    <i
                        class="fas fa-exclamation-triangle mr-2"
                        aria-hidden="true"
                    ></i>
                    Whoops! | The fishcam is gone...
                </div>

                <div class="card-body">
                    <p class="card-text">
                        Unfortunately the fishcam seems to be unavailable at the
                        moment. Please let the board know and try again later!
                    </p>
                </div>
            </div>

            <div
                id="fishcam"
                class="mb-3 card bg-transparent d-none h-100 w-auto"
            >
                <img
                    class="d-none h-100 ml-auto"
                    id="fishcam-src"
                    style="object-fit: contain"
                />
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const fishcam = document.getElementById('fishcam')
        const fishcamSrc = document.getElementById('fishcam-src')
        const activate = document.getElementById('fishcam-activate')
        const warning = document.getElementById('fishcam-warning')
        const unavailable = document.getElementById('fishcam-unavailable')

        activate.addEventListener('click', () => {
            fishcamSrc.src = '{{ Config::string('app-proto.fishcam-url') }}'
            fishcamSrc.classList.remove('d-none')
            warning.classList.add('d-none')
        })

        fishcamSrc.addEventListener('error', () => {
            unavailable.classList.remove('d-none')
            fishcam.classList.add('d-none')
        })

        fishcamSrc.addEventListener('load', () => {
            fishcam.classList.remove('d-none')
        })
    </script>
@endpush
