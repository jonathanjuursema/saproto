<div class="card mb-3">

    <div class="card-header bg-dark text-white mb-1">
        WallstreetDrink Event Product overview
    </div>
    <form method="post" action="{{ route('wallstreet::events::products::create', ['id'=>$currentEvent->id]) }}">

        @csrf
        <div class="row mx-2 mb-3">
            <label for="product">Product(s):</label>
            <div class="col-9">
                <div class="form-group autocomplete">
                    <input class="form-control product-search" id="product" name="product[]" multiple required>
                </div>
            </div>
            <div class="col-3">
                <button class="btn btn-outline-primary btn-block" type="submit">
                    <i class="fas fa-plus-circle"></i>
                </button>
            </div>
        </div>
    </form>

    @if(count($currentEvent->products) > 0)
        <div>
            <div class="card-footer">
                @foreach($currentEvent->products as $product)
                    <a href="{{ route('wallstreet::events::products::remove', ['id' => $currentEvent->id, 'productId' => $product->id]) }}">
                        <span class="badge rounded-pill bg-warning">
                            {{ $product->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

    @else

        <div class="text-center text-muted py-3">
            There are products associated with this Event yet!
        </div>

    @endif

</div>

