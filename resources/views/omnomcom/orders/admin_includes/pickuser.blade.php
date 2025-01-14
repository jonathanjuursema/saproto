<form method="get" action="{{ route('omnomcom::orders::filter::name') }}">
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Orderlines from specific user
        </div>

        <div class="card-body">
            <div class="form-group autocomplete">
                <label for="user">User:</label>
                <input
                    class="form-control user-search"
                    id="user"
                    name="user"
                    data-label="User:"
                    required
                />
            </div>
        </div>

        <div class="card-footer">
            <input
                type="submit"
                class="btn btn-success btn-block"
                value="Get orders"
            />
        </div>
    </div>
</form>
