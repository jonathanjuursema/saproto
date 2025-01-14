@extends('website.layouts.redesign.generic')

@section('page-title')
        Mollie Transaction #{{ $transaction->id }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                </div>

                <table class="table table-borderless table-sm">
                    <tr>
                        <th>User</th>
                        <td>{{ $transaction->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $mollie->description }}</td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>
                            &euro;{{ number_format($mollie->amount->value, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if (App\Models\MollieTransaction::translateStatus($mollie->status) == 'open')
                                <a href="{{ $transaction->payment_url }}">
                                    <span class="label label-success">
                                        {{ $mollie->status }} - Continue
                                        Payment
                                    </span>
                                </a>
                            @elseif (App\Models\MollieTransaction::translateStatus($mollie->status) == 'paid')
                                <span class="label label-success">
                                    {{ $mollie->status }}
                                </span>
                            @elseif (App\Models\MollieTransaction::translateStatus($mollie->status) == 'failed')
                                <span class="label label-danger">
                                    {{ $mollie->status }}
                                </span>
                            @else
                                <span class="label label-warning">
                                    {{ $mollie->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="card-body">
                    @if (App\Models\MollieTransaction::translateStatus($mollie->status) == 'failed')
                        <p>
                            This payment has failed. All orderlines associated
                            with this payment have been set back to unpaid. You
                            can try to start a new payment.
                        </p>
                    @else
                        <p>
                            Below you can find all the orderlines associated
                            with this payment.
                        </p>

                        <table
                            class="table table-hover table-borderless table-sm"
                        >
                            <thead>
                                <tr>
                                    <th>€</th>
                                    <th>Product</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->orderlines as $orderline)
                                    <tr>
                                        <td>
                                            <strong>&euro;</strong>
                                            {{ number_format($orderline->total_price, 2, '.', '') }}
                                        </td>
                                        <td>
                                            {{ $orderline->units }}x
                                            <strong>
                                                {{ $orderline->product->name }}
                                            </strong>
                                        </td>
                                        <td>
                                            {{ date('Y-m-d H:i:s', strtotime($orderline->created_at)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="card-footer">
                    <a
                        href="{{ url()->previous() }}"
                        class="btn btn-default btn-block"
                    >
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
