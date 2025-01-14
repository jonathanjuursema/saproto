@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $company->name }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card mb-3">
                @if ($company->image)
                    <div class="card-header text-center">
                        <div
                            class="align-items-center row"
                            style="height: 200px"
                        >
                            <div class="col d-block">
                                <img
                                    class="company-{{ strtolower($company->name) }}"
                                    src="{{ $company->image->generateImagePath(null, null) }}"
                                    style="max-width: 70%; max-height: 160px"
                                    alt="logo of {{ $company->name }}"
                                />
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card-body">
                    <h2 class="card-title">
                        {{ $company->name }}
                    </h2>

                    <div class="mt-3">
                        {!! Markdown::convert($company->description) !!}
                    </div>

                    <a href="{{ $company->url }}" class="card-link text-info">
                        Visit website
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
