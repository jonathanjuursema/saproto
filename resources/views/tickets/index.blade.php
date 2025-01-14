@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Tickets Admin
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                    <a
                        href="{{ route('tickets::create') }}"
                        class="badge bg-info float-end"
                    >
                        Create a new ticket.
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr class="bg-dark text-white">
                                <td>Event</td>
                                <td>Ticket Name</td>
                                <td>Availability</td>
                                <td>Who</td>
                                <td>Buy limit</td>
                                <td>Sold</td>
                                <td>Controls</td>
                            </tr>
                        </thead>

                        @foreach ($tickets as $ticket)
                            <tr>
                                <td style="max-width: 200px">
                                    @if ($ticket->event)
                                        <a
                                            href="{{ route('event::show', ['id' => $ticket->event->getPublicId()]) }}"
                                        >
                                            {{ $ticket->event->title }}
                                        </a>
                                    @endif
                                </td>
                                <td style="max-width: 200px">
                                    <a
                                        href="{{ route('omnomcom::products::edit', ['id' => $ticket->product->id]) }}"
                                    >
                                        {{ $ticket->product->name }}
                                    </a>
                                </td>
                                <td style="min-width: 200px">
                                    from
                                    <i>
                                        {{ date('d-m-Y H:i', $ticket->available_from) }}
                                    </i>
                                    <br />
                                    till
                                    <i>
                                        {{ date('d-m-Y H:i', $ticket->available_to) }}
                                    </i>
                                </td>
                                <td>
                                    {{ $ticket->members_only ? 'Members' : 'Everyone' }}
                                </td>
                                <td>
                                    @if ($ticket->has_buy_limit)
                                        {{ $ticket->buy_limit }}
                                    @endif
                                </td>
                                <td>
                                    {{ $ticket->sold() }} /
                                    {{ $ticket->totalAvailable() }}
                                </td>
                                <td>
                                    <a
                                        href="{{ route('tickets::edit', ['id' => $ticket->id]) }}"
                                    >
                                        <i
                                            class="fas fa-edit"
                                            aria-hidden="true"
                                        ></i>
                                    </a>
                                    <a
                                        class="text-danger ms-2"
                                        href="{{ route('tickets::delete', ['id' => $ticket->id]) }}"
                                    >
                                        <i
                                            class="fas fa-trash"
                                            aria-hidden="true"
                                        ></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="card-footer pb-0">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
