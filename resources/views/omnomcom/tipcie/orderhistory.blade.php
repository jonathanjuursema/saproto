@extends('website.layouts.redesign.dashboard')

@section('page-title')
    TIPCie Order History
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-3">

            <form method="get" action="{{ route('omnomcom::tipcie::orderhistory') }}">

                <div class="card">

                    <div class="card-header bg-dark text-white">
                        TIPCie Orderline History
                    </div>

                    <div class="card-body">

                        <p class="card-text">
                            @if(!$date)
                                Today's orders
                            @else
                                Orderlines of {{ $date }}
                            @endif

                            <br/>

                            <i>A day starts at 6am</i>
                        </p>

                        <hr>

                        <label>Orderlines from:</label>
                        @include('website.layouts.macros.datetimepicker', [
                            'name' => 'date',
                            'format' => 'date',
                            'placeholder' => date('U')
                        ])

                    </div>

                    <div class="card-footer">
                        <input type="submit" class="btn btn-success btn-block" value="Get orders">
                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-4">

            <div class="card">

                @if(count($orders) > 0)
                    <table class="table table-borderless table-hover">
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->name }}</td>
                                <td>{{ $order->amount }}</td>
                                <td>&euro; {{ number_format($order->totalPrice, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td><strong>{{ $dailyAmount }}</strong></td>
                            <td><strong>&euro; {{ number_format($dailyTotal, 2) }}</strong></td>
                        </tr>
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="card-text text-center">No orders for the specified date.</p>
                    </div>
                @endif

            </div>

        </div>

    </div>

@endsection