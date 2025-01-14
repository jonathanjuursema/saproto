@extends('website.master')

@section('page-title')
    S.A. Proto | Narrowcasting
@endsection

@push('stylesheet')
    <style>
        html {
            background-color: #333;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }
    </style>
@endpush

@section('body')
    @include('narrowcasting.display')
@endsection
