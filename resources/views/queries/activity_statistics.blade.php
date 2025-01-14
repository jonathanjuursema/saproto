@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Actual membership totals
@endsection

@section('container')
    <div class="row d-inline-flex justify-content-center w-100">
        <div class="col-10">
            <div class="card mb-3">
                <div class="card-header">
                    <form method="get">
                        <div class="row">
                            <label
                                for="datetimepicker-start"
                                class="col-sm-auto col-form-label pe-0"
                            >
                                Start:
                            </label>
                            <div class="col-sm-auto">
                                @include(
                                    'components.forms.datetimepicker',
                                    [
                                        'name' => 'start',
                                        'format' => 'date',
                                        'placeholder' => $start,
                                    ]
                                )
                            </div>
                            <label
                                for="datetimepicker-start"
                                class="col-sm-auto col-form-label pe-0"
                            >
                                End:
                            </label>
                            <div class="col-sm-auto">
                                @include(
                                    'components.forms.datetimepicker',
                                    [
                                        'name' => 'end',
                                        'format' => 'date',
                                        'placeholder' => $end,
                                    ]
                                )
                            </div>

                            <div class="col-sm-auto">
                                <button type="submit" class="btn btn-success">
                                    Find activities!
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th colspan="2">Number of activities</th>
                                <th>Sign ups</th>
                                <th>Attendees</th>
                                <th>Cost</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Total</th>
                                <th>Percentage</th>
                                <th>Percentage of spots filled</th>
                                <th>Show percentage</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($eventCategories as $category)
                                <tr>
                                    <td>
                                        {{ $category->name }}
                                    </td>
                                    {{-- nr events --}}
                                    <td>{{ $category->events_count }}</td>
                                    {{-- % events --}}
                                    <td>
                                        {{ round(($category->events_count / $totalEvents) * 100, 2) }}
                                    </td>
                                    {{-- % signups --}}
                                    @if ($category->spots == 0)
                                        <td>N/A</td>
                                    @else
                                        <td>
                                            {{ round(($category->signups / $category->spots) * 100, 2) }}
                                        </td>
                                    @endif
                                    {{-- % show --}}
                                    @if ($category->signups == 0)
                                        <td>N/A</td>
                                    @else
                                        <td>
                                            {{ round(($category->attendees / $category->signups) * 100, 2) }}
                                        </td>
                                    @endif
                                    {{-- Avg costs --}}
                                    <td>
                                        €{{ round($category->average_cost(), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Events per month per board</h4>
                </div>
                <div class="card-body">
                    <div
                        class="w-100"
                        style="position: relative; margin-left: auto"
                    >
                        <canvas id="chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    {{-- chart.js and the date adapter --}}
    <script
        nonce="{{ csp_nonce() }}"
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>
    <script
        nonce="{{ csp_nonce() }}"
        src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"
    ></script>

    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        const ctx = document.getElementById('chart')
        var chart = null
        var data = {!! json_encode($events->toArray(), JSON_HEX_TAG) !!}

        function createDataSets(data) {
            let myData = {
                datasets: [],
            }
            Object.values(data).forEach((product) => {
                let prices = []
                product.forEach((item) => {
                    date = new Date(item.Start * 1000)
                    date = date.setFullYear(
                        date.getFullYear(),
                        date.getMonth(),
                        1
                    )
                    prices.push({
                        x: date,
                        y: item.Total,
                    })
                })
                myData.datasets.push({ label: product[0].Board, data: prices })
            })
            return myData
        }

        console.log('creating chart')

        chart = new Chart(ctx, {
            type: 'line',
            options: {
                offset: true,
                spanGaps: true,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'month',
                        },
                        parsing: false,
                    },
                },
                responsive: true,
            },
            data: createDataSets(data),
        })
    </script>
@endpush
