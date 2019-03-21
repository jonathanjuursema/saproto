@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($joboffer == null ? "Create new job offer." : "Edit job offer " . $joboffer->title .".") }}
@endsection

@section('container')

    <form method="post"
          action="{{ ($joboffer == null ? route("joboffers::add") : route("joboffers::edit", ['id' => $joboffer->id])) }}"
          enctype="multipart/form-data">

        {!! csrf_field() !!}

        <div class="row justify-content-center">

            <div class="col-md-4">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="company">Company</label>
                            <select id="company" name="company_id" class="form-control" required>
                                <option value="" @if($joboffer == null) selected @endif disabled>Select a company...
                                </option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}"
                                            @if($joboffer && $joboffer->company->id == $company->id) selected @endif>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   placeholder="Chief Executive Officer" value="{{ $joboffer->title or '' }}" required>
                        </div>


                        <div class="form-group">
                            <label for="editor-description">Description</label>
                            @include('website.layouts.macros.markdownfield', [
                                'name' => 'description',
                                'placeholder' => !$joboffer ? 'A text dedicated to the job offer. Be as elaborate as you need to be!' : null,
                                'value' => !$joboffer ? null : $joboffer->description
                            ])
                        </div>

                    </div>

                    <div class="card-footer">
                        <a class="btn btn-default" href="{{ route("joboffers::admin") }}">Cancel</a>
                        <button type="submit" class="btn btn-success float-right">Save</button>
                    </div>

                </div>

            </div>

        </div>

    </form>

@endsection