@extends('website.layouts.redesign.generic')

@section('page-title')
    Dinner Form Admin
@endsection

@section('container')
    <div class="row">
        <div class="col-xl-4">

            @include('dinnerform.admin_includes.dinnerform-details')

        </div>
        <div class="col-xl-8">

            @include('dinnerform.admin_includes.dinnerform-list')

        </div>
    </div>
@endsection