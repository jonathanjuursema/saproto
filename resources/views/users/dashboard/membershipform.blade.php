@extends('website.layouts.redesign.generic')

@section('page-title')
    Sign membership contract
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        window.addEventListener('load', (_) => {
            const signatureAlert = document.getElementById('signature-alert')
            const canvas = document.getElementById('signature-pad')
            const signaturePad = new SignaturePad(canvas)

            window.addEventListener('resize', resizeCanvas)
            resizeCanvas()

            function resizeCanvas() {
                // When zoomed out to less than 100%, for some very strange reason,
                // some browsers report devicePixelRatio as less than 1
                // and only part of the canvas is cleared then.
                const ratio = Math.max(window.devicePixelRatio || 1, 1)
                canvas.width = canvas.offsetWidth * ratio
                canvas.height = canvas.offsetHeight * ratio
                canvas.getContext('2d').scale(ratio, ratio)
                signaturePad.clear()
            }

            document.getElementById('clear').addEventListener('click', (_) => {
                signaturePad.clear()
            })

            document
                .getElementById('signature-form')
                .addEventListener('submit', (e) => {
                    if (signaturePad.isEmpty()) {
                        e.preventDefault()
                        signatureAlert.classList.remove('d-none')
                    } else {
                        document.getElementById('signature').value =
                            signaturePad.toDataURL('image/png')
                    }
                })
        })
    </script>
@endpush

@section('container')
    <div id="membership-contract" class="row justify-content-center">
        <div class="col-lg-6 col-md-7">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    @include('users.includes.membershipform_include')

                    <b>Signature:</b>
                    <div class="wrapper">
                        <canvas id="signature-pad"></canvas>
                        <button
                            id="clear"
                            class="btn btn-danger position-absolute m-2 px-2 py-1"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div
                        id="signature-alert"
                        class="alert alert-danger text-center d-none p-1 my-2 mx-4"
                        role="alert"
                    >
                        We need your signature for the membership contract to be
                        valid!
                    </div>

                    <p>
                        {{ $user->name }}
                        <br />
                        Enschede, {{ date('M, d, Y', strtotime('today')) }}
                    </p>

                    <form
                        id="signature-form"
                        method="POST"
                        action="{{ route('memberform::sign') }}"
                    >
                        @csrf
                        <input type="hidden" id="signature" name="signature" />
                        <input type="submit" class="btn btn-info" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
