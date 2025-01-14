@extends('website.layouts.redesign.generic')

@section('page-title')
    Interesting companies
@endsection

@section('container')
    <div class="row row-eq-height justify-content-center">
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

                        <div class="mt-3">
                            {!! Markdown::convert($company->excerpt) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
