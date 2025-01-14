@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Unwithdrawable orderlines
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    <p class="card-text">
                        These orderlines cannot be withdrawn because they are
                        associated with a user that doesn't have an active
                        withdrawal authorization.
                    </p>
                </div>

                <table class="table table-hover table-sm">
                    <thead>
                        <tr class="bg-dark text-white">
                            <td></td>
                            <td>Name</td>
                            <td>Total</td>
                            <td>Transaction Date</td>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $key => $user)
                            <tr
                                class="cursor-pointer"
                                data-bs-toggle="collapse"
                                data-bs-target=".collapse-{{ $key }}"
                            >
                                <th>{{ $key }}</th>
                                <td>{{ $user->user->name }}</td>
                                <th>
                                    &euro; {{ number_format($user->total, 2) }}
                                </th>
                                <td></td>
                            </tr>

                            @foreach ($user->orderlines as $orderline)
                                <tr class="collapse collapse-{{ $key }}">
                                    <td>{{ $orderline->id }}</td>
                                    <td>
                                        {{ $orderline->units }}x
                                        {{ $orderline->product->name }}
                                    </td>
                                    <td>
                                        &euro;
                                        {{ number_format($orderline->total_price, 2) }}
                                    </td>
                                    <td>{{ $orderline->created_at }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
