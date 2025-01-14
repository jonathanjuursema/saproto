<form
    class="form-horizontal"
    method="post"
    action="{{ route('feedback::store', ['category' => $category]) }}"
>
    {{ csrf_field() }}

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Add your own {{ str_singular($category->title) }}
        </div>

        <div class="card-body">
            <label for="new-feedback">
                New {{ str_singular($category->title) }}:
            </label>
            <textarea
                id="new-feedback"
                class="form-control"
                rows="4"
                cols="30"
                name="feedback"
                required
                placeholder="Your {{ strtolower(str_singular($category->title)) }} goes here."
            ></textarea>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success btn-block">
                Submit {{ str_singular($category->title) }}
            </button>
        </div>
    </div>
</form>
