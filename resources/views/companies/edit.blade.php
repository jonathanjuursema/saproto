@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($company == null ? "Create new company." : "Edit company " . $company->name .".") }}
@endsection

@section('container')

    <form method="post"
          action="{{ ($company == null ? route("companies::store") : route("companies::update", ['id' => $company->id])) }}"
          enctype="multipart/form-data">

        {!! csrf_field() !!}

        <div class="row justify-content-center">

            <div class="col-md-4">

                <div class="card md-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">

                        @if($company?->image)

                            <div class="text-center">
                                <img src="{!! $company->image->generateImagePath(500, null) !!}"
                                     style="max-height: 100px;">
                            </div>

                            <hr>

                        @endif

                        <div class="form-group">
                            <label for="name">Company name:</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Aperture Science" value="{{ $company->name ?? '' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="url">Company website:</label>
                            <input type="text" class="form-control" id="url" name="url"
                                   placeholder="http://www.aperturescience.com/" value="{{ $company->url ?? '' }}"
                                   required>
                        </div>

                        <hr>

                        @include('components.forms.checkbox', [
                            'name' => 'on_carreer_page',
                            'checked' => $company?->on_carreer_page,
                            'label' => 'Visible on the career page'
                        ])

                        @include('components.forms.checkbox', [
                            'name' => 'in_logo_bar',
                            'checked' => $company?->in_logo_bar,
                            'label' => 'Place logo in the logo bar'
                        ])

                        @include('components.forms.checkbox', [
                            'name' => 'on_membercard',
                            'checked' => $company?->on_membercard,
                            'label' => 'Visible on membercard page'
                        ])

                        <hr>

                        <div class="custom-file">
                            <input id="image" type="file" class="form-control" name="image">
                            <label class="form-label" for="image">Upload a new image</label>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success float-end">
                            Submit
                        </button>

                        <a href="{{ route("companies::admin") }}" class="btn btn-default">
                            Cancel
                        </a>
                    </div>

                </div>

            </div>

            <div class="col-md-8">

                <div class="card md-3">

                    <div class="card-header bg-dark text-white">
                        Description
                    </div>

                    <div class="card-body row">

                        <div class="col-6">
                            <label for="editor">Company excerpt</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'excerpt',
                                'placeholder' => !$company ? 'A small paragraph about this company.' : null,
                                'value' => !$company ? null : $company->excerpt
                            ])
                        </div>

                        <div class="col-6">
                            <label for="editor">Company description</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'description',
                                'placeholder' => !$company ? 'A text dedicated to the company. Be as elaborate as you need to be!' : null,
                                'value' => !$company ? null : $company->description
                            ])
                        </div>

                        <div class="col-6">
                            <label for="editor">Membercard excerpt</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'membercard_excerpt',
                                'placeholder' => !$company ? 'A small paragraph about what this company does on our membercard.' : null,
                                'value' => !$company ? null : $company->membercard_excerpt
                            ])
                        </div>

                        <div class="col-6">
                            <label for="editor">Membercard description</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'membercard_long',
                                'placeholder' => !$company ? 'A text dedicated to the companies role for our membercard. Be as elaborate as you need to be!' : null,
                                'value' => !$company ? null : $company->membercard_long
                            ])
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

@endsection
