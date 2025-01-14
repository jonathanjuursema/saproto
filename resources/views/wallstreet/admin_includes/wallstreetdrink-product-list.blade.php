<div class="card mb-3">
    <div class="card-header bg-dark text-white mb-1">
        WallstreetDrink Product overview
    </div>
    @if (! $currentDrink->is_active)
        <form
            method="post"
            action="{{ route('wallstreet::products::create', ['id' => $currentDrink->id]) }}"
        >
            @csrf
            <div class="row mx-2 mb-3">
                <label for="product">Product(s):</label>
                <div class="col-9">
                    <div class="form-group autocomplete">
                        <input
                            class="form-control product-search"
                            id="product"
                            name="product[]"
                            multiple
                            required
                        />
                    </div>
                </div>
                <div class="col-3">
                    <button
                        class="btn btn-outline-primary btn-block"
                        type="submit"
                    >
                        <i class="fas fa-plus-circle"></i>
                    </button>
                </div>
            </div>
        </form>
    @endif

    @if (count($currentDrink->products) > 0)
        <div>
            <div class="card-footer">
                @foreach ($currentDrink->products as $product)
                    <a
                        href="{{ route('wallstreet::products::remove', ['id' => $currentDrink->id, 'productId' => $product->id]) }}"
                    >
                        <span class="badge rounded-pill bg-warning">
                            {{ $product->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center text-muted py-3">
            There are products associated with this drink yet!
        </div>
    @endif
</div>
