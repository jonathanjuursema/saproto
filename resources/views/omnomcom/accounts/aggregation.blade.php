@extends('website.layouts.redesign.dashboard')

@section('page-title')
    <strong>{{ $account->name }}</strong> account aggregation
@endsection

@section('container')

    <div class="row justify-content-center mb-5">

        <div class="col-md-4">

            <div class="card">

                <div class="card-header">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    <p class="card-textØ">
                        This table aggregates the total sales values for each product in the
                        <strong>{{ $account->name }}</strong> account between <strong>{{ $start }} - {{ $end }}</strong>.
                    </p>
                </div>

                <table class="table table-hover table-sm">

                    <thead>
                    <tr>
                        <th class="pl-4">Product</th>
                        <th>Units</th>
                        <th>Turnover</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($aggregation as $key => $product)
                        <tr>
                            <td class="pl-4">{{ $product->name }}</td>
                            <td>{{ $product->number_sold }}</td>
                            <td>&euro; {{ number_format($product->total_turnover, 2) }}</td>
                        </tr>
                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection