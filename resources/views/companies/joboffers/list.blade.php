@extends('website.layouts.redesign.generic')

@section('page-title')
    Interesting job offers
@endsection

@section('container')
    <div class="row row-eq-height">
        @if (count($companies) > 0)
            @foreach ($companies as $key => $company)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header text-center">
                            <div
                                class="align-items-center row"
                                style="height: 120px"
                            >
                                <div class="col d-block">
                                    @if ($company->image)
                                        <img
                                            src="{{ $company->image->generateImagePath(null, null) }}"
                                            style="
                                                max-width: 70%;
                                                max-height: 100px;
                                            "
                                        />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $company->name }}</h5>

                            <a
                                href="{{ route('companies::show', ['id' => $company->id]) }}"
                                class="card-link text-info"
                            >
                                Learn more
                            </a>
                            <a
                                href="{{ $company->url }}"
                                class="card-link text-info"
                            >
                                Visit website
                            </a>

                            <p class="card-text mt-3">
                                This company is currently looking to fill these
                                positions:
                            </p>

                            <ul class="list-group">
                                @foreach ($company->joboffers as $joboffer)
                                    <a
                                        href="{{ $joboffer->redirect_url ?? route('joboffers::show', ['id' => $joboffer->id]) }}"
                                        class="list-group-item leftborder-info leftborder"
                                    >
                                        {{ $joboffer->title }}
                                    </a>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            There are currently no job offers. Please check back
                            soon! :)
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
