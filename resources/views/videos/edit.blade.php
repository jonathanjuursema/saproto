@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Edit video
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-3">
            <form
                method="post"
                action="{{ route('video::admin::update', ['id' => $video->id]) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                @include(
                                    'components.forms.datetimepicker',
                                    [
                                        'name' => 'video_date',
                                        'label' => 'Video date:',
                                        'format' => 'date',
                                        'placeholder' => strtotime($video->video_date),
                                    ]
                                )
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group autocomplete">
                                    <label for="event">Link to event:</label>
                                    <input
                                        id="event"
                                        class="form-control event-search"
                                        name="event"
                                    />
                                </div>
                            </div>
                        </div>

                        @if ($video->event)
                            <p class="text-center">
                                Currently linked to:
                                <br />
                                <strong>
                                    {{ $video->event->title }}
                                    ({{ date('d-m-Y', $video->event->start) }})
                                </strong>
                            </p>
                        @endif

                        <hr />

                        <img
                            src="{{ $video->youtube_thumb_url }}"
                            alt="video thumbnail"
                            width="100%"
                        />
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('video::admin::index') }}"
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
                </div>
            </form>
        </div>
    </div>
@endsection
