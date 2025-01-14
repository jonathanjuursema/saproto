@extends('website.layouts.redesign.dashboard')

@section('page-title')
    TIPCie Order History
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-3">
            <form
                method="get"
                action="{{ route('omnomcom::tipcie::orderhistory') }}"
            >
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        TIPCie Orderline History
                    </div>

                    <div class="card-body">
                        <p class="card-text">
                            @if (! $date)
                                Today's orders
                            @else
                                Orderlines of {{ $date }}
                            @endif

                            <br />

                            <i>A day starts at 6am</i>
                        </p>

                        <hr />

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'date',
                                'label' => 'Orderlines from:',
                                'placeholder' => date('U'),
                                'format' => 'date',
                            ]
                        )
                    </div>

                    <div class="card-footer">
                        <input
                            type="submit"
                            class="btn btn-success btn-block"
                            value="Get orders"
                        />
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                @if (count($products) > 0)
                    <table class="table table-borderless table-hover">
                        <tbody>
                            @php
                                /**@var \App\Models\Product $product **/
                            @endphp

                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        {{ $product->orderlines_sum_units }}
                                    </td>
                                    <td>
                                        &euro;
                                        {{ number_format($product->orderlines_sum_total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td></td>
                                <td><strong>{{ $totalAmount }}</strong></td>
                                <td>
                                    <strong>
                                        &euro;
                                        {{ number_format($totalPrice, 2) }}
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="card-text text-center">
                            No orders for the specified date.
                        </p>
                    </div>
                @endif
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    Recorded PIN Transactions
                    <span class="float-end">
                        &euro; {{ number_format($pinTotal, 2) }}
                    </span>
                </div>

                @if (count($pinOrders) > 0)
                    <table class="table table-borderless table-hover">
                        <tbody>
                            @foreach ($pinOrders as $pinOrder)
                                <tr>
                                    <td>{{ $pinOrder->created_at }}</td>
                                    <td>
                                        &euro;
                                        {{ number_format($pinOrder->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="card-text text-center">
                            No PIN orders recorded for the specified date.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
