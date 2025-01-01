<div id="controls" class="fixed-bottom bg-omnomcom align-content-center p-2 mb-4">

    <button id="purchase" class="purchase-button btn btn-lg bg-dark float-end" disabled>
        <i class="fas fa-cookie-bite me-2"></i> Complete order
    </button>
    @if($store['cash_allowed'])
        <button id="purchase-cash-initiate" class="purchase-button btn btn-lg bg-dark me-2 float-end" disabled>
            <i class="fas fa-coins me-2"></i> Complete with cash
        </button>
    @endif
    @if($store['bank_card_allowed'])
        <button id="purchase-bank-card-initiate" class="purchase-button btn btn-lg bg-dark me-2 float-end" disabled>
            <i class="fas fa-credit-card me-2"></i> Complete with PIN
        </button>
    @endif

    <span id="rfid" class="btn btn-lg bg-dark me-2 float-end">Link RFID card</span>
    <span class="info float-end font-weight-bold me-3 my-auto">Order total: &euro;<span id="total">0.00</span></span>
    <span id="status" class="info float-end me-3 inactive">RFID Service: Disconnected</span>

    <div id="cart">
        <div id="cart-overflow" class="stretched-link bg-white">
            <div class="cart-product-image">
                <div class="cart-product-image-inner"
                     style="background-image: url({{ asset('images/font-awesome/fa-cart-shopping.svg') }});"></div>
            </div>
            <div class="cart-product-count">0x</div>
        </div>
    </div>

</div>
