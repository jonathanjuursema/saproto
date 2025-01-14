<form
    method="get"
    action="{{ route('feedback::search', ['category' => $category->url]) }}"
>
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Search for {{ $category->title }}
        </div>

        <div class="card-body">
            <div class="form-group">
                Search term:
                <input
                    class="form-control"
                    id="searchTerm"
                    name="searchTerm"
                    placeholder="{{ Auth::user()->calling_name }}"
                    value="{{ $searchTerm ?? '' }}"
                    required
                />
            </div>
        </div>

        <div class="card-footer">
            <input
                type="submit"
                class="btn btn-success btn-block"
                value="Search {{ $category->title }}"
            />
        </div>
    </div>
</form>
