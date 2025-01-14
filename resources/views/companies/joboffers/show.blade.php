@extends('website.layouts.redesign.generic')

@section('page-title')
        {{ $joboffer->company->name }}: {{ $joboffer->title }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card mb-3">
                @if ($joboffer->company->image)
                    <div class="card-header text-center">
                        <div
                            class="align-items-center row"
                            style="height: 200px"
                        >
                            <div class="col d-block">
                                <img
                                    src="{{ $joboffer->company->image->generateImagePath(null, null) }}"
                                    style="max-width: 70%; max-height: 160px"
                                />
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card-body">
                    <h3>{{ $joboffer->title }}</h3>

                    <h6 class="card-subtitle mb-3 text-muted">
                        Posted on
                        {{ date_format($joboffer->created_at, 'd-m-Y') }} for
                        <a
                            href="{{ route('companies::show', ['id' => $joboffer->company->id]) }}"
                            class="text-info"
                        >
                            {{ $joboffer->company->name }}
                        </a>
                        .
                    </h6>

                    {!! Markdown::convert($joboffer->description) !!}
                </div>
            </div>
        </div>
    </div>
@endsection
