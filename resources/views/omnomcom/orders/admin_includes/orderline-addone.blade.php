<a class="btn btn-success btn-block mb-3" href="{{route('omnomcom::orders::orderline-wizard')}}">
    Add orderline wizard. <span class="ms-3">🧙</span>
</a>

<form method="post" action="{{ route('omnomcom::orders::store') }}">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Add an order
        </div>

        <div class="card-body">
            <div class="form-group autocomplete">
                <label for="user">User(s):</label>
                <input class="form-control user-search" id="user" name="user[]" data-label="User(s):" multiple required>
            </div>
            <div class="form-group autocomplete">
                <label for="product">Product(s):</label>
                <input class="form-control product-search" id="product" name="product[]" multiple required>
            </div>
        </div>

        <div class="card-footer">
            <input type="submit" class="btn btn-success btn-block" value="Save">
        </div>

    </div>


</form>
