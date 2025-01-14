<form method="get" action="{{ route('email::filter') }}">
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">Search emails</div>

        <div class="card-body row">
            <div class="form-group col-7">
                <label for="searchterm">Searchterm:</label>
                <input
                    class="form-control"
                    id="searchterm"
                    name="searchterm"
                    value="{{ $searchTerm ?? '' }}"
                    required
                />
            </div>

            <div class="col">
                @include(
                    'components.forms.checkbox',
                    [
                        'name' => 'search_description',
                        'label' => 'Search description',
                        'checked' => $description ?? true,
                    ]
                )
                @include(
                    'components.forms.checkbox',
                    [
                        'name' => 'search_subject',
                        'label' => 'Search subject',
                        'checked' => $subject ?? false,
                    ]
                )
                @include(
                    'components.forms.checkbox',
                    [
                        'name' => 'search_body',
                        'label' => 'Search body',
                        'checked' => $body ?? false,
                    ]
                )
            </div>
        </div>

        <div class="card-footer">
            <input
                type="submit"
                class="btn btn-success btn-block"
                value="Search emails"
            />
        </div>
    </div>
</form>
