@extends('website.layouts.redesign.generic')

@section('page-title')
    {{ $page->title }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-md-12 col-sm-12">
            <div class="card mb-3">
                @if ($page->featuredImage)
                    <img
                        class="card-img-top w-100"
                        alt="featured image"
                        src="{{ $page->featuredImage->generateImagePath('1000', '200') }}"
                    />
                @endif

                <div class="card-header">
                    <h3 class="card-title m-0">
                        @yield('page-title')
                        @can('board')
                            <a
                                href="{{ route('page::edit', ['id' => $page->id]) }}"
                                class="btn btn-info py-1 float-end"
                            >
                                edit
                                <i class="fa fa-edit"></i>
                            </a>
                        @endcan
                    </h3>
                </div>

                <div class="card-body">
                    {!! $parsedContent !!}

                    @if ($page->files->count() > 0 && $page->show_attachments)
                        <hr />

                        <p>
                            <strong>Attachments</strong>

                            @foreach ($page->files as $file)
                                <a
                                    href="{{ $file->generatePath() }}"
                                    target="_blank"
                                    class="card-link"
                                >
                                    <i
                                        class="fas fa-paperclip"
                                        aria-hidden="true"
                                    ></i>
                                    {{ $file->original_filename }}
                                </a>
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
