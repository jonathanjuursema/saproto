@php
    /**@var \App\Models\Event $event */
@endphp

@if ($event->tickets()->count() > 0)
    <?php $has_unpaid_tickets = false; ?>

    @if ($event->hasBoughtTickets(Auth::user()))
        <div class="card mb-3">
            <div class="card-header ellipsis">
                Your tickets for {{ $event->title }}
            </div>

            <div class="card-body">
                @foreach ($event->getTicketPurchasesFor(Auth::user()) as $i => $purchase)
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="card-title">
                                <span
                                    class="badge bg-dark text-white float-end"
                                >
                                    #{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <strong>
                                    {{ $purchase->ticket->product->name }}
                                </strong>
                                (&euro;{{ number_format($purchase->orderline->total_price, 2) }})

                                @if ($purchase->canBeDownloaded())
                                    <a
                                        href="{{ route('tickets::download', ['id' => $purchase->id]) }}"
                                        class="card-link text-info"
                                    >
                                        Download PDF
                                    </a>
                                @else
                                    <?php $has_unpaid_tickets = true; ?>

                                    <a
                                        class="card-link text-danger"
                                        href="{{ $purchase->orderline->molliePayment->payment_url ?? route('omnomcom::orders::index') }}"
                                    >
                                        Payment Required
                                    </a>
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($has_unpaid_tickets)
                <div class="card-footer text-center">
                    <strong class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-fw me-2"></i>
                        Attention!
                    </strong>
                    <br />
                    You have unpaid tickets. You need to pay for your tickets
                    before you can download and use them. Unpaid tickets will be
                    invalidated if payment takes too long.
                </div>
            @endif
        </div>
    @endif

    <form
        method="post"
        action="{{ route('event::buytickets', ['id' => $event->id]) }}"
    >
        @csrf

        <div class="card mb-3">
            @php
                $has_prepay_tickets = false;
                $tickets_available = 0;
                $only_prepaid = true;
            @endphp

            <div class="card-header ellipsis">
                Buy tickets for {{ $event->title }}
            </div>

            <div class="card-body">
                @if (! Auth::check())
                    <p class="card-text">
                        Please
                        <a
                            href="{{ route('event::login', ['id' => $event->getPublicId()]) }}"
                        >
                            log-in
                        </a>
                        to buy tickets.
                    </p>
                @else
                    @foreach ($event->tickets as $ticket)
                        @php
                            /** @var \App\Models\Ticket $ticket */
                        @endphp

                        <div
                            class="card mb-3 {{ $ticket->isAvailable(Auth::user()) ? '' : 'opacity-50' }}"
                        >
                            <div class="card-body">
                                <p class="card-title">
                                    @if ($ticket->buyLimitReached(Auth::user()))
                                        <span class="badge float-end bg-danger">
                                            Buy limit reached
                                        </span>
                                    @elseif ($ticket->is_prepaid || ! Auth::user()->is_member)
                                        @php
                                            $has_prepay_tickets = true;
                                        @endphp

                                        <span class="badge bg-danger float-end">
                                            Pre-Paid
                                        </span>
                                    @else
                                        @php
                                            $only_prepaid = false;
                                        @endphp

                                        <span class="badge bg-info float-end">
                                            Withdrawal
                                        </span>

                                        @if ($ticket->has_buy_limit)
                                            <span
                                                class="badge bg-warning float-end mx-3"
                                            >
                                                {{ $ticket->buy_limit }} max
                                                per user
                                            </span>
                                        @endif
                                    @endif

                                    <strong>
                                        {{ $ticket->product->name }}
                                    </strong>
                                    (&euro;{{ number_format($ticket->product->price, 2) }})
                                </p>

                                <p class="card-text">
                                    @if ($ticket->isAvailable(Auth::user()))
                                        <span class="badge bg-info float-end">
                                            {{ $ticket->product->stock > Config::integer('proto.maxtickets') ? Config::integer('proto.maxtickets') . '+' : $ticket->product->stock }}
                                            available
                                        </span>
                                    @endif

                                    @if (date('U') > $ticket->available_to)
                                        Not for sale anymore.
                                    @elseif (date('U') < $ticket->available_from)
                                        For sale starting
                                        {{ date('d-m-Y H:i', $ticket->available_from) }}
                                    @elseif (! $ticket->canBeSoldTo(Auth::user()) && ! Auth::user()->is_member)
                                        This ticket is only available to members!
                                    @elseif ($ticket->product->stock <= 0)
                                        Sold-out!
                                    @else
                                        @php
                                            $tickets_available++;
                                        @endphp

                                        <strong>On sale!</strong>
                                        <br />
                                        Available until
                                        {{ date('d-m-Y H:i', $ticket->available_to) }}
                                    @endif

                                    @if ($ticket->isAvailable(Auth::user()))
                                        <select
                                            required
                                            class="form-control ticket-select"
                                            name="tickets[{{ $ticket->id }}]"
                                            autocomplete="off"
                                            data-price="{{ $ticket->product->price }}"
                                            data-prepaid="{{ $ticket->is_prepaid }}"
                                            data-previous-value="0"
                                        >
                                            @php
                                                $max = min(Config::integer('proto.maxtickets'), $ticket->product->stock);
                                                if ($ticket->has_buy_limit) {
                                                    $max = min($max, $ticket->BuyLimitForUser(Auth::user()));
                                                }
                                            @endphp

                                            @for ($i = 0; $i <= $max; $i++)
                                                <option value="{{ $i }}">
                                                    {{ $i }}x
                                                </option>
                                            @endfor
                                        </select>
                                    @endif

                                    @if ($ticket->show_participants)
                                        <i>
                                            Note: with this ticket your name
                                            will be visible on the event page
                                        </i>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{--
                5 cases (pp = prepaid, npp = not prepaid)
                1: pp, npp and fees
                2: pp, npp and no fees
                3: pp, and fees
                4: pp and no fees
                5: npp
            --}}
            @if (Auth::check() && $tickets_available > 0)
                <div class="card-footer">
                    {{-- No fees of no prepaid (2,4,5) --}}
                    @if (! Config::boolean('omnomcom.mollie.use_fees') || ! $has_prepay_tickets)
                        <button type="submit" class="btn btn-success btn-block">
                            Total:
                            <strong>
                                &euro;
                                <span id="ticket-total" class="mr-3">0.00</span>
                            </strong>
                            Finish purchase!
                        </button>
                        {{-- fees and only prepaid (3) --}}
                    @elseif (Config::boolean('omnomcom.mollie.use_fees') && $only_prepaid)
                        @include('event.display_includes.mollie-modal')
                        <a
                            href="javascript:void();"
                            class="btn btn-primary btn-block"
                            data-bs-toggle="modal"
                            data-bs-target="#mollie-modal"
                        >
                            Get tickets now!
                        </a>
                    @else
                        <button
                            id="directpay"
                            type="submit"
                            class="btn btn-success btn-block"
                        >
                            Total:
                            <strong>
                                &euro;
                                <span id="ticket-total" class="mr-3">0.00</span>
                            </strong>
                            Finish purchase!
                        </button>
                        @include('event.display_includes.mollie-modal')
                        <a
                            hidden
                            id="feesbutton"
                            href="javascript:void();"
                            class="btn btn-primary btn-block"
                            data-bs-toggle="modal"
                            data-bs-target="#mollie-modal"
                        >
                            Get tickets now!
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </form>

    @foreach ($event->tickets as $ticket)
        @if ($ticket->show_participants)
            <div class="card mb-3">
                <div class="card-header">
                    Participants who bought
                    <b>{{ $ticket->product->name }}</b>
                    tickets
                </div>
                <div class="card-body">
                    @include(
                        'event.display_includes.render_participant_list',
                        [
                            'participants' => $ticket->getUsers(),
                            'event' => null,
                        ]
                    )
                </div>
            </div>
        @endif
    @endforeach

    @push('javascript')
        <script type="text/javascript" nonce="{{ csp_nonce() }}">
            const directPayButton = document.getElementById('directpay')
            const feesButton = document.getElementById('feesbutton')
            const selectList = Array.from(
                document.getElementsByClassName('ticket-select')
            )
            let totalPrepaidSelected = 0
            selectList.forEach((ticket) =>
                ticket.addEventListener('change', (_) => {
                    const total = selectList.reduce(
                        (agg, el) =>
                            agg + el.getAttribute('data-price') * el.value,
                        0
                    )
                    document.getElementById('ticket-total').innerHTML =
                        total.toFixed(2)

                    if (ticket.getAttribute('data-prepaid') === true) {
                        totalPrepaidSelected +=
                            ticket.value - ticket.getAttribute('previous-value')
                        ticket.setAttribute('data-previous-value', ticket.value)
                    }
                    if (totalPrepaidSelected === 0) {
                        directPayButton?.setAttribute('hidden', '')
                        feesButton?.removeAttribute('hidden')
                    } else if (totalPrepaidSelected > 0) {
                        directPayButton?.removeAttribute('hidden')
                        feesButton?.setAttribute('hidden', '')
                    }
                })
            )
        </script>
    @endpush
@endif
