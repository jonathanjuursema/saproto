@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Orderline Administration
@endsection

@section('container')
    <div class="row">
        <div class="col-md-3 col-md-push-9">
            @include('omnomcom.orders.admin_includes.orderline-addone')

            @include('omnomcom.orders.admin_includes.pickdate')

            @include('omnomcom.orders.admin_includes.pickuser')
        </div>

        <div class="col-md-9 col-md-3">
            @include('omnomcom.orders.admin_includes.history')
        </div>
    </div>
@endsection
