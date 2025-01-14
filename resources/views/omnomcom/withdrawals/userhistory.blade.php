@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Personal Overview for the {{ date('d-m-Y', strtotime($withdrawal->date)) }}
    Withdrawal
    <br />
    Withdrawal status: {{ $withdrawal->closed ? 'Closed' : 'Paid' }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10 col-sm-12">
            <div class="card mb-3 bg-dark text-white">
                <div class="card-body">
                    @if ($withdrawal->getFailedWithdrawal(Auth::user()))
                        <div class="alert alert-danger text-center">
                            <i
                                class="fas fa-times fa-fw me-2"
                                aria-hidden="true"
                            ></i>
                            This withdrawal has failed.
                        </div>
                    @endif

                    <p class="card-text text-center">
                        Withdrawal total:
                        &euro;{{ number_format($withdrawal->totalForUser(Auth::user()), 2, '.', ',') }}
                    </p>
                </div>
            </div>

            @if (count($orderlines) > 0)
                @include('omnomcom.orders.includes.history')
            @else
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text text-center">
                            You are not included in this withdrawal.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
