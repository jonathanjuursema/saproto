@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Change a Short URL
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mb-3">
                <form
                    action="{{ ! empty($url) ? route('short_urls.update', ['short_url' => $url]) : route('short_urls.store') }}"
                    method="POST"
                >
                    <input
                        type="hidden"
                        name="_method"
                        value="{{ ! empty($url) ? 'PUT' : 'POST' }}"
                    />
                    {{ csrf_field() }}

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input
                                type="text"
                                class="form-control"
                                id="description"
                                name="description"
                                placeholder="I don't want to type long URLs..."
                                value="{{ $url ? $url->description : '' }}"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label for="url">Short URL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        {{ route('short_urls.go', ['short' => null]) }}/
                                    </span>
                                </div>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="url"
                                    name="url"
                                    placeholder="awesome-music"
                                    required=""
                                    value="{{ $url ? $url->url : '' }}"
                                />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="target">Target</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        https://
                                    </span>
                                </div>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="target"
                                    name="target"
                                    placeholder="www.youtube.com/watch?v=M11SvDtPBhA"
                                    required=""
                                    value="{{ $url ? $url->target : '' }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a
                            href="{{ route('short_urls.index') }}"
                            class="btn btn-default"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
