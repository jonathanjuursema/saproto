@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ $product == null ? 'Create new product.' : 'Edit product ' . $product->name . '.' }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            @include('omnomcom.products.edit_includes.edit')
        </div>

        @if ($product)
            <div class="col-md-4">
                @include('omnomcom.products.edit_includes.purchases')
            </div>
        @endif
    </div>
@endsection
