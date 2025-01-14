@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ $title }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-dark text-white mb-1">
                    @yield('page-title')
                </div>

                <table class="table table-hover table-sm">
                    <thead>
                        <tr class="bg-dark text-white">
                            <td></td>
                            <td>Name</td>
                            <td></td>
                            <td>Total</td>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($accounts as $account_id => $dates)
                            @php
                                $accountTotal = number_format(
                                    $dates->sum(function ($date) {
                                        return $date->sum('total');
                                    }),
                                    2,
                                );
                            @endphp

                            <tr
                                class="cursor-pointer"
                                data-bs-toggle="collapse"
                                data-bs-target=".collapse-{{ $account_id }}"
                            >
                                <th>{{ $account_id }}</th>
                                <td>
                                    {{ $dates->first()->first()->account_name }}
                                </td>
                                <td></td>
                                <td>&euro; {{ $accountTotal }}</td>
                            </tr>

                            @foreach ($dates as $date => $products)
                                <tr
                                    class="cursor-pointer collapse collapse-{{ $account_id }}"
                                    data-bs-toggle="collapse"
                                    data-bs-target=".innercollapse-{{ $account_id }}-{{ $date }}"
                                >
                                    <td></td>
                                    <td>{{ $date }}</td>
                                    <td></td>
                                    <td>
                                        &euro;
                                        {{ number_format($products->sum('total'), 2) }}
                                    </td>
                                </tr>
                                @foreach ($products as $product)
                                    <tr
                                        class="collapse innercollapse innercollapse-{{ $account_id }}-{{ $date }}"
                                    >
                                        <td></td>
                                        <td></td>
                                        <td>{{ $product->product_name }}</td>
                                        <td>
                                            &euro;{{ number_format($product->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach

                        @if (isset($total))
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="text-end">Total</td>
                                <td class="font-weight-bold">
                                    &euro; {{ number_format($total, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        window.addEventListener('load', (_) => {
            const dayList = Array.from(
                document.getElementsByClassName('collapse')
            )
            dayList.forEach((day) => {
                day.addEventListener('hide.bs.collapse', (_) => {
                    const children = [
                        ...document.getElementsByClassName(
                            day.getAttribute('data-bs-target').slice(1)
                        ),
                    ]
                    children.forEach((child) => {
                        child.classList.remove('show')
                    })
                })
            })
        })
    </script>
@endpush
