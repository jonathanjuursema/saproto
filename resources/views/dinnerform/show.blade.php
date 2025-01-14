@extends('website.layouts.redesign.generic')

@section('page-title')
    Dinner Form
@endsection

@section('container')
    <div class="row justify-content-center">
        @if ($dinnerform->isCurrent() && ! $dinnerform->hasOrdered())
            <div class="col-12 col-sm-6">
                @include('dinnerform.includes.order-details')
            </div>
        @endif

        @isset($order)
            <div class="col-12 col-sm-4">
                @include('dinnerform.includes.order')
            </div>
        @endisset
    </div>
@endsection
