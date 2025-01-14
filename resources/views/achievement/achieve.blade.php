@extends('website.layouts.redesign.generic')

@section('page-title')
    Achieve {{ $achievement->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-md-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i
                            class="fas fa-{{ $achievement->fa_icon }} fa-fw me-2"
                        ></i>
                        {{ $achievement->name }}
                    </h5>
                </div>

                <div class="card-body">
                    <p class="card-text">
                        {!! $parsed_content !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
