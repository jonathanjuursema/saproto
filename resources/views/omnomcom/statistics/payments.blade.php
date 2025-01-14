@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Payment statistics
@endsection

@section('container')
    <div class="row justify-content-center mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    @yield('page-title')
                </div>

                <div class="card-body">
                    <p>
                        Between
                        <strong>{{ $start }}</strong>
                        and
                        <strong>{{ $end }}</strong>
                        the following payments took place:
                    </p>

                    <p>
                        <i class="fas fa-fw fa-coins me-2"></i>
                        Cash payments:
                        &euro;{{ number_format($total_cash, 2) }}
                    </p>

                    <p>
                        <i class="fas fa-fw fa-credit-card me-2"></i>
                        Card payments:
                        &euro;{{ number_format($total_card, 2) }}
                    </p>
                </div>

                <div class="card-footer">
                    <a
                        href="{{ route('omnomcom::payments::statistics') }}"
                        class="btn btn-success btn-block"
                    >
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
