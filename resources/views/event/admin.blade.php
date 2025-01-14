@extends('website.layouts.redesign.generic')

@section('page-title')
    Event Admin
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <a
                href="{{ route('event::checklist', ['id' => $event->id]) }}"
                class="btn btn-success btn-block mb-3"
            >
                Participant Checklist
            </a>

            @if ($event->shouldShowDietInfo())
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Diet and allergy information.
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                @foreach ($event->allUsers() as $user)
                                    @if ($user->hasDiet())
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                {!! Markdown::convert($user->diet) !!}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        @if (count($event->tickets) > 0)
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        Event tickets
                    </div>

                    <div class="card-body">
                        <a
                            class="btn btn-success btn-block disabled"
                            href="{{ route('event::scan', ['id' => $event->id]) }}"
                        >
                            Start Scanner Application for Event
                        </a>
                        <i>
                            The ticket scanner is temporarily disabled due to
                            technical issues.
                        </i>

                        <hr />

                        @foreach ($event->tickets as $ticket)
                            <div class="card mb-3">
                                <div
                                    class="card-header bg-dark text-white cursor-pointer"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#ticket-collapse-{{ $ticket->id }}"
                                >
                                    Ticket
                                    <strong>
                                        {{ $ticket->product->name }}
                                    </strong>
                                    <span class="badge bg-primary float-end">
                                        {{ $ticket->sold() }} sold /
                                        {{ $ticket->product->stock }} available
                                    </span>
                                    <span class="badge bg-info float-end me-4">
                                        &euro;{{ number_format($ticket->turnover(), 2) }}
                                    </span>
                                </div>

                                <div
                                    class="collapse"
                                    id="ticket-collapse-{{ $ticket->id }}"
                                >
                                    <div class="card-body">
                                        @if ($ticket->purchases->count() > 0)
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>User</th>
                                                        <th></th>
                                                        @if ($event->shouldShowDietInfo())
                                                            <th>Diet</th>
                                                        @endif

                                                        <th>Price</th>
                                                        <th>
                                                            Date of Purchase
                                                        </th>
                                                        <th>Ticket Scanned</th>
                                                        @can('board')
                                                            <th></th>
                                                        @endcan
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($ticket->purchases as $purchase)
                                                        <tr>
                                                            <td>
                                                                {{ $purchase->id }}
                                                            </td>
                                                            <td>
                                                                {{ $purchase->user->name }}
                                                            </td>
                                                            <td>
                                                                @if ($purchase->user->age() >= 18)
                                                                    <span
                                                                        class="badge bg-success"
                                                                    >
                                                                        <i
                                                                            class="fas fa-check"
                                                                            aria-hidden="true"
                                                                        ></i>
                                                                        18+
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-danger"
                                                                    >
                                                                        <i
                                                                            class="fas fa-exclamation-triangle"
                                                                            aria-hidden="true"
                                                                        ></i>
                                                                        18-
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            @if ($event->shouldShowDietInfo())
                                                                <td>
                                                                    @if ($purchase->user->hasDiet())
                                                                        <span
                                                                            class="badge bg-danger"
                                                                        >
                                                                            <i
                                                                                class="fas fa-exclamation-triangle"
                                                                                aria-hidden="true"
                                                                            ></i>
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="label label-success"
                                                                        >
                                                                            <i
                                                                                class="badge bg-check"
                                                                                aria-hidden="true"
                                                                            ></i>
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            @endif

                                                            <td>
                                                                &euro;{{ number_format($purchase->orderline->total_price, 2) }}
                                                            </td>
                                                            <td>
                                                                {{ $purchase->created_at }}
                                                            </td>
                                                            <td
                                                                class="events-scanned"
                                                            >
                                                                <a
                                                                    data-id="{{ $purchase->barcode }}"
                                                                    class="{{ $purchase->scanned ? 'unscan' : 'scan' }} dontprint"
                                                                    href="#"
                                                                >
                                                                    {{ $purchase->scanned ? 'Unscan' : 'Scan Manually' }}
                                                                </a>
                                                            </td>
                                                            @can('board')
                                                                <td
                                                                    class="dontprint"
                                                                >
                                                                    @if ($purchase->scanned)
                                                                        Used
                                                                    @elseif ($purchase->orderline->isPayed())
                                                                        Paid
                                                                    @else
                                                                        @include(
                                                                            'components.modals.confirm-modal',
                                                                            [
                                                                                'action' => route('omnomcom::orders::delete', [
                                                                                    'id' => $purchase->orderline->id,
                                                                                ]),
                                                                                'classes' => 'badge bg-danger',
                                                                                'text' => 'Delete',
                                                                                'title' => 'Confirm Delete',
                                                                                'message' =>
                                                                                    'Are you sure you want to delete one ticket for ' .
                                                                                    $purchase->user->name .
                                                                                    '?',
                                                                            ]
                                                                        )
                                                                    @endif
                                                                </td>
                                                            @endcan
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="card-text text-center">
                                                This ticket has not sold yet.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        window.addEventListener('load', (_) => {
            const scanList = Array.from(document.getElementsByClassName('scan'))
            const unscanList = Array.from(
                document.getElementsByClassName('unscan')
            )
            scanList.forEach((el) => setEventListener(el, false))
            unscanList.forEach((el) => setEventListener(el, true))
        })

        const scanRequest = (barcode, unscan) =>
            get('{{ route('api::scan', ['event' => $event->id]) }}', {
                barcode: barcode,
                ...(unscan && { unscan: true }),
            })

        function setEventListener(el, unscan) {
            el.addEventListener('click', (e) => {
                e.preventDefault()
                let barcode = e.target.getAttribute('data-id')
                let parent = e.target.parentElement
                if (barcode === undefined) throw new Error("Can't find barcode")
                scanRequest(barcode, unscan)
                    .then((_) => {
                        console.log('Scanned barcode ' + barcode)
                        let link = document.createElement('a')
                        link.href = '#'
                        link.setAttribute('data-id', barcode)
                        link.innerHTML = unscan ? 'Scan Manually' : 'Unscan'
                        link.className = unscan
                            ? 'scan dontprint'
                            : 'unscan dontprint'
                        parent.innerHTML = ''
                        parent.append(link)
                        setEventListener(link, !unscan)
                    })
                    .catch((err) => {
                        console.error(err)
                        window.alert("Couldn't register scan.")
                    })
            })
        }
    </script>
@endpush
