<div
    id="finished-modal"
    class="modal fade"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1 class="mb-3">Your purchase has completed.</h1>
                <h2 id="finished-modal-message"></h2>
                <div id="finished-modal-continue" class="btn btn-omnomcom">
                    Continue
                </div>
                <video id="purchase-movie" width="473" height="260">
                    <source
                        src="{{ asset('videos/omnomcom.webm') }}"
                        type="video/webm"
                    />
                </video>
                <audio
                    id="purchase-audio"
                    src="{{-- Content inserted by JavaScript --}}"
                    type="audio/mpeg"
                ></audio>
            </div>
        </div>
    </div>
</div>

<div
    id="rfid-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center m-4">
                <span class="modal-status">
                    {{-- Content inserted by JavaScript --}}
                </span>
            </div>
        </div>
    </div>
</div>

<div
    id="outofstock-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1>The product you selected is out of stock.</h1>
            </div>
        </div>
    </div>
</div>

<div
    id="idlewarning-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1>Timeout warning!</h1>
                <span class="modal-status">
                    If you want to continue using the OmNomCom,
                    <br />
                    please touch the screen.
                </span>
            </div>
        </div>
    </div>
</div>

<div
    id="emptycart-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1>
                    The cart is empty. Please fill the cart before scanning your
                    card!
                </h1>
            </div>
        </div>
    </div>
</div>

<div
    id="badcard-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1 class="text-danger">Not so hasty!</h1>
                <span class="modal-status">
                    It looks like you've scanned your card incorrectly. Please
                    close this window, try again and hold your card close to the
                    reader!
                </span>
            </div>
        </div>
    </div>
</div>

<div
    id="randcard-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1 class="text-danger">Unsupported card!</h1>
                <span class="modal-status">
                    It seems you are trying to scan a randomized NFC card. Make
                    sure to
                    <b>not</b>
                    use a government issued card or a mobile phone.
                </span>
            </div>
        </div>
    </div>
</div>

<div
    id="purchase-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h1>Complete your purchase</h1>
                <div class="qrAuth" style="min-height: 350px">
                    Loading QR authentication...
                </div>
                <hr />
                <span class="modal-status">
                    Authenticate using the QR code or present an RFID card.
                </span>
            </div>
        </div>
    </div>
</div>
