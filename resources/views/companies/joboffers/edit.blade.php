@extends('website.layouts.redesign.dashboard')

@section('page-title')
    {{ ($joboffer == null ? "Create new job offer." : "Edit job offer " . $joboffer->title .".") }}
@endsection

@section('container')

    <form method="post"
          action="{{ ($joboffer == null ? route("joboffers::store") : route("joboffers::update", ['id' => $joboffer->id])) }}"
          enctype="multipart/form-data">

        @csrf

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
                                            @if($joboffer?->company->id == $company->id) selected @endif>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   placeholder="Chief Executive Officer" value="{{ $joboffer->title ?? '' }}" required>
                        </div>

                        <div class="form-group">
                            <label for="information_type_selector">Offer information type</label>
                            <select id="information_type_selector" class="form-control">
                                <option value=""
                                        @if(!$joboffer || ($joboffer->description == null && $joboffer->redirect_url == null)) selected
                                        @endif disabled>Select a type...
                                </option>
                                <option value="description"
                                        @if($joboffer?->description != null) selected @endif>Description
                                </option>
                                <option value="url" @if($joboffer?->redirect_url != null) selected @endif>
                                    Redirect URL
                                </option>
                            </select>
                        </div>

                        <div id="information_type_description" class="form-group">
                            <label for="editor-description">Description</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'description',
                                'placeholder' => !$joboffer ? 'A text dedicated to the job offer. Be as elaborate as you need to be!' : null,
                                'value' => !$joboffer ? null : $joboffer->description
                            ])
                        </div>

                        <div id="information_type_url" class="form-group">
                            <label for="redirect_url">Redirect URL</label>
                            <input type="text" class="form-control" id="redirect_url" name="redirect_url"
                                   placeholder="https://example.com/apply" value="{{ $joboffer->redirect_url ?? '' }}">
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>

                    <div class="card-footer">
                        <a class="btn btn-default" href="{{ route("joboffers::admin") }}">Cancel</a>
                        <button type="submit" class="btn btn-success float-end">Save</button>
                    </div>

                </div>

            </div>

        </div>

    </form>

@endsection

@push('javascript')

    <script nonce="{{ csp_nonce() }}">
        const typeUrl = document.getElementById('information_type_url');
        const redirectUrl = document.getElementById('redirect_url');
        const typeDescription = document.getElementById('information_type_description');
        const typeSelector = document.getElementById('information_type_selector');
        const easymde = window.easyMDEFields['markdownfield-description'];

        updateInformationDisplay();
        typeSelector.addEventListener('change', updateInformationDisplay);

        function updateInformationDisplay() {
            switch (typeSelector.value) {
                case 'description':
                    typeDescription.classList.remove('d-none');
                    typeUrl.classList.add('d-none');
                    redirectUrl.value = '';
                    redirectUrl.required = false;
                    break;
                case 'url':
                    typeDescription.classList.add('d-none');
                    typeUrl.classList.remove('d-none');
                    redirectUrl.required = true;
                    easymde.value('');
                    break;
                default:
                    typeUrl.classList.add('d-none');
                    typeUrl.querySelector('input').value = '';
                    typeDescription.classList.add('d-none');
                    typeDescription.querySelectorAll('input').value = '';
                    typeSelector.required = true;
                    easymde.value('');
                    break;
            }
        }
    </script>

@endpush
