<div
    id="mollie-modal"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle">
                    Pay tickets
                </h4>
                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-justify">
                <p>
                    Using this service you can pay your tickets using our
                    external payment provider Mollie. Using Mollie you can pay
                    your tickets using
                    @foreach ($payment_methods as $method)
                        @if ($loop->last)
                            {{ $method->description . '.' }}
                        @else
                            {{ $method->description . ', ' }}
                        @endif
                    @endforeach
                </p>

                <p>
                    <strong>Important!</strong>
                    Using this service you will incur a transaction fee on top
                    of your selected tickets for some methods. This transaction
                    will appear in your OmNomCom history after payment. Hover on
                    a payment method's icon to see the transaction fee.
                </p>
            </div>
            <div class="modal-body text-left container">
                Available payment methods
                <div
                    class="row justify-content-around btn-group-toggle mb-2"
                    data-bs-toggle="buttons"
                >
                    @include(
                        'omnomcom.mollie.list-all-payment-methods',
                        [
                            'methods' => $payment_methods,
                            'use_fees' => true,
                        ]
                    )
                </div>
            </div>

            <div class="modal-footer">
                <div class="col-3">
                    <button
                        type="button"
                        class="btn btn-default btn-block"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                </div>
                <div class="col-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        Pay
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
