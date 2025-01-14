@extends('website.layouts.redesign.generic')

@section('page-title')
        Purchase Overview for {{ date('F Y', strtotime($selected_month)) }}
@endsection

@section('container')
    <div class="row">
        <div class="col-xl-2 col-lg-4 col-md-12">
            @include('omnomcom.orders.includes.payment-details')
        </div>

        <div class="col-xl-6 col-lg-8 col-md-12">
            @include('omnomcom.orders.includes.history')
        </div>

        <div class="col-xl-2 col-lg-6 col-md-12">
            @include('omnomcom.orders.includes.recent-payments')
        </div>

        <div class="col-xl-2 col-lg-6 col-md-12">
            @include('omnomcom.orders.includes.month-overview')
        </div>
    </div>

    @include('omnomcom.orders.includes.mollie-modal')
@endsection
