@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Wallsteet drink chart!
@endsection

@section('container')
    <div style="position: relative; height:90vh; width:95vw; margin-left:auto">
        <canvas id="myChart"></canvas>
    </div>
@endsection

@vite('resources/assets/js/echo.js')

@push('javascript')
    {{--    chart.js and the date adapter--}}
    <script nonce="{{ csp_nonce() }}" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script nonce="{{ csp_nonce() }}"
            src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <script nonce="{{ csp_nonce() }}">
        // Initialize when page is loaded
        window.addEventListener('load', _ => {
            const ctx = document.getElementById('myChart');

            get(`{{route('api::wallstreet::all_prices', ['id'=>$id])}}`).then((products) => {

                var chart = new Chart(ctx, {
                    type: "line",
                    options: {
                        maintainAspectRatio: false,
                        spanGaps: true,
                        scales: {
                            x: {
                                type: "time",
                                parsing: false
                            }
                        },
                        responsive: true,
                    },
                    data: {
                        datasets: products.map((product) => {
                            return {
                                label: product.name,
                                data: product.wallstreet_prices.map((price) => {
                                    return {
                                        x: Date.parse(price.created_at),
                                        y: price.price
                                    }
                                })
                            }
                        })
                    },
                });

                let id = {{$id}};
                //listen to a new wallstreet price
                Echo.private(`wallstreet-prices.${id}`)
                    .listen('NewWallstreetPrice', (e) => {
                        const dataset = chart.data.datasets.find((dataset) => dataset.label === e.data.product.name)
                        if (dataset) {
                            dataset.data.push({
                                x: Date.parse(e.data.created_at),
                                y: e.data.price
                            });
                        } else {
                            //if a new product is added the dataset is created
                            chart.data.datasets.push({
                                label: e.data.product.name,
                                data: [{
                                    x: Date.parse(e.data.created_at),
                                    y: e.data.price
                                }]
                            })
                        }
                        chart.update('none');
                    });
            });
        });

    </script>
@endpush
