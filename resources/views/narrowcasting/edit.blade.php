@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ $item == null ? 'Create new campaign.' : 'Edit campaign ' . $item->name . '.' }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mb-3">
                <form
                    method="post"
                    action="{{ $item == null ? route('narrowcasting::store') : route('narrowcasting::update', ['id' => $item->id]) }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Campaign name:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Lightsaber Building in the SmartXp"
                                value="{{ $item->name ?? '' }}"
                                required
                            />
                        </div>

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'campaign_start',
                                'label' => 'Campaign start:',
                                'placeholder' => $item ? $item->campaign_start : date('U'),
                            ]
                        )

                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'campaign_end',
                                'label' => 'Campaign end:',
                                'placeholder' => $item ? $item->campaign_end : null,
                            ]
                        )

                        <div class="form-group">
                            <label for="slide_duration">
                                Slide duration in seconds:
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="slide_duration"
                                name="slide_duration"
                                value="{{ $item->slide_duration ?? '30' }}"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label for="youtube_id">YouTube ID:</label>
                            <input
                                type="text"
                                class="form-control"
                                id="youtube_id"
                                name="youtube_id"
                                placeholder="Only the ID!"
                                value="{{ $item->youtube_id ?? '' }}"
                            />
                        </div>

                        <p>
                            <sup>
                                <strong>Note:</strong>
                                if a YouTube ID is present, the image file and
                                slide duration field is ignored and hidden.
                            </sup>

                            @if ($item?->youtube_id)
                                <label>Current video:</label>

                                <div class="row">
                                    <iframe
                                        height="300"
                                        src="https://www.youtube.com/embed/{{ $item->youtube_id }}"
                                        allow="encrypted-media"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            @else
                                <div class="custom-file mb-3">
                                    <input
                                        id="image"
                                        type="file"
                                        class="form-control"
                                        name="image"
                                    />
                                    <label class="form-label">
                                        Upload an image
                                    </label>
                                </div>

                                <p>
                                    <sup>
                                        <strong>Images should be</strong>
                                        1366 x 768 pixels.
                                    </sup>

                                    @if ($item?->image)
                                        <label>Current image:</label>
                                        <img
                                            src="{!! $item->image->generateImagePath(500, null) !!}"
                                            class="w-100"
                                        />
                                    @endif
                                </p>
                            @endif
                        </p>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('narrowcasting::index') }}"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>

                        <p class="text-center mb-0 mt-2">
                            Developed with
                            <span class="text-danger">
                                <i class="fab fa-youtube fa-fw"></i>
                                YouTube
                            </span>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
