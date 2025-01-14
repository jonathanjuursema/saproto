<div class="card mb-3">
    <div class="card-header bg-dark text-white">Financial details</div>

    <div class="card-body">
        <p class="card-text">
            Total spent in {{ date('F Y', strtotime($selected_month)) }}
        </p>

        <h3 class="card-title">
            &euro; {{ number_format($total, 2, '.', '') }}
        </h3>

        @if ($next_withdrawal > 0)
            <p class="card-text">Next withdrawal</p>

            <h3 class="card-title">
                &euro; {{ number_format($next_withdrawal, 2, '.', '') }}
            </h3>
        @elseif ($next_withdrawal < 0)
            <p class="card-text">OmNomCom credit left</p>

            <h3 class="card-title">
                &euro; {{ number_format(-$next_withdrawal, 2, '.', '') }}
            </h3>
        @endif

        @if ($outstandingAmount > 0)
            <div class="border-bottom-0">
                <span class="w-100 d-inline-flex justify-content-between">
                    <span
                        class="cursor-pointer"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-outstanding"
                    >
                        Remaining outstanding
                        <i
                            class="fas fa-info-circle"
                            data-bs-toggle="tooltip"
                            data-bs-placement="right"
                            title="The amount owed for activities you signed up for that have not yet been processed by the treasurer. Therefore, you can not pay this yet."
                        ></i>
                        <i class="fas fa-sm fa-fw fa-caret-down"></i>
                    </span>
                </span>
                <h3 class="card-title d-inline-block">
                    &euro; {{ number_format($outstandingAmount, 2, '.', '') }}
                </h3>

                <div id="collapse-outstanding" class="collapse">
                    <div class="cursor-default p-0">
                        <table
                            class="table table-borderless table-responsive table-sm mt-1"
                        >
                            <thead>
                                <tr>
                                    <th scope="col">Activity</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($outstanding as $outstandingActivity)
                                    <tr>
                                        <td>
                                            {{ $outstandingActivity->event->title }}
                                        </td>
                                        <td>
                                            &euro;
                                            {{ number_format($outstandingActivity->price, 2, '.', '') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($next_withdrawal > 0)
        <div class="card-footer">
            <a
                href="#"
                class="btn btn-primary btn-block"
                data-bs-toggle="modal"
                data-bs-target="#mollie-modal"
            >
                Pay now
            </a>
        </div>
    @elseif ($next_withdrawal < 0)
        <div class="card-footer">
            You won't be charged as long as you have OmNomCom credit.
        </div>
    @endif
</div>

<script nonce="{{ csp_nonce() }}">
    const outstanding = document.querySelector(
        '[data-bs-target="#collapse-outstanding"]'
    )
    outstanding.addEventListener('click', () => {
        const outstanding_caret = outstanding.querySelector('.fa-caret-down')
        outstanding_caret.classList.toggle('fa-rotate-180')
    })
</script>
