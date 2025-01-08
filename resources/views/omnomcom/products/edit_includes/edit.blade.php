<div class="card">

    <form method="post"
          action="{{ ($product == null ? route("omnomcom::products::store") : route("omnomcom::products::update", ['id' => $product->id])) }}"
          enctype="multipart/form-data">

        <div class="card-header bg-dark text-white">
            @yield('page-title')
        </div>

        @csrf

        <div class="card-body">

            @if($product != null && $product->image != null)

                <div class="d-flex justify-content-center">

                    <div class="product-image bg-dark mb-2">

                        <img src="{!! $product->image->generateImagePath(null, null) !!}">

                    </div>

                </div>

                <hr>

            @endif

            <div class="row mb-3">

                <div class="col-md-6">

                    <label for="name">Product name:</label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Bertie Bott's Every Flavour Beans"
                           value="{{ $product->name ?? '' }}"
                           required>

                </div>

                <div class="col-md-6">

                    <label for="price">Unit price:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">&euro;</span>
                        </div>
                        <input type="text" class="form-control" id="price" name="price"
                               placeholder="0" value="{{ $product ? number_format($product->price, 2) : '' }}" required>
                    </div>

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-6">

                    <label for="name">Supplier ID:</label>
                    <input type="text" class="form-control" id="supplier_id" name="supplier_id"
                           placeholder="12345678" value="{{ $product->supplier_id ?? '' }}">

                </div>

                <div class="col-md-6">

                    <label for="supplier_collo">
                        Collo size
                        <span data-bs-toggle="tooltip" data-bs-placement="top"
                              title="The amount of units in a package when bought. (e.g.: there are 24 cans in a tray)">(?)</span>
                        :
                    </label>
                    <input type="number" class="form-control" id="supplier_collo" name="supplier_collo"
                           placeholder="0" value="{{ $product->supplier_collo ?? '' }}">

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-3">

                    <label for="stock">Shelf stock:<i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip"
                                                      data-bs-placement="right"
                                                      title="This is the amount of the product before you stock the shelves. This amount is for checking the stock for theft. If you are only reporting theft, make sure that the final stock is set to the same value."></i></label>
                    <input type="number" class="form-control" id="prev_stock" name="prev_stock"
                           placeholder="0" value="{{ $product->stock ?? '' }}">

                </div>

                <div class="col-md-3">

                    <label for="stock">Final stock:<i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip"
                                                      data-bs-placement="right"
                                                      title="This is the updated amount, put your new stock amounts here."></i></label>
                    <input type="number" class="form-control" id="stock" name="stock"
                           placeholder="0" value="{{ $product->stock ?? '' }}">

                </div>

                <div class="col-md-3">

                    <label for="preferred_stock">Preferred stock:</label>
                    <input type="number" class="form-control" id="preferred_stock" name="preferred_stock"
                           placeholder="0" value="{{ $product->preferred_stock ?? '' }}">

                </div>

                <div class="col-md-3">

                    <label for="max_stock">Maximum stock:</label>
                    <input type="number" class="form-control" id="max_stock" name="max_stock"
                           placeholder="0" value="{{ $product->max_stock ?? '' }}">

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-4">
                    <label for="price">Calories:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="calories" name="calories" placeholder="0"
                               value="{{ $product->calories ?? '' }}">
                    </div>
                </div>

            </div>
            @include('components.forms.checkbox', [
                'name' => 'is_visible',
                'checked' => $product?->is_visible,
                'label' => 'Visible in OmNomCom.'
            ])

            @include('components.forms.checkbox', [
                'name' => 'is_visible_when_no_stock',
                'checked' => $product?->is_visible_when_no_stock,
                'label' => 'Visible in OmNomCom even when out of stock.'
            ])

            @include('components.forms.checkbox', [
                'name' => 'is_alcoholic',
                'checked' => $product?->is_alcoholic,
                'label' => 'Product contains alcohol.'
            ])

            <hr>

            <div class="row">

                <div class="col-md-6">

                    <label for="max_stock">Product categories:</label>

                    <select multiple name="product_categories[]" class="form-control">

                        @foreach($categories as $catogory)

                            <option
                                value="{{ $catogory->id }}" @selected($product != null && $product->categories->contains($catogory))>
                                {{ $catogory->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label for="max_stock">Financial account:</label>

                    <select name="account_id" class="form-control" required>

                        @foreach($accounts as $account)

                            <option
                                value="{{ $account?->id }}" @selected(old('account_id', $account && $product && $account->id == $product->account_id))>
                                {{ $account->name }} ({{ $account->account_number }})
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <hr>

            <div class="custom-file">
                <input type="file" class="form-control" id="image" name="image">
                <label class="form-label" for="image">Update product image</label>
            </div>

        </div>

        <div class="card-footer clearfix">


            @if($product)
                @include('components.modals.confirm-modal', [
                   'action' => route('omnomcom::products::delete', ['id' => $product->id]),
                   'classes' => 'btn btn-danger',
                   'text' => 'Delete',
                   'title' => 'Confirm Delete',
                   'message' => "Are you sure you want to delete $product->name?",
                ])
            @endif

            <button type="submit" class="btn btn-success float-end ms-3">Submit</button>

            <a href="{{ route("omnomcom::products::index") }}" class="btn btn-default float-end">Cancel</a>

            @if ($product?->ticket)
                <a href="{{ route('tickets::edit', ['id' => $product->ticket->id]) }}"
                   class="btn btn-default float-end">
                    Go to event ticket
                </a>
            @endif

        </div>

    </form>

</div>
