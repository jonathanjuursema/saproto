<div class="card">

    <form method="post"
          action="{{ ($product == null ? route("omnomcom::products::add") : route("omnomcom::products::edit", ['id' => $product->id])) }}"
          enctype="multipart/form-data">

        <div class="card-header bg-dark text-white">
            @yield('page-title')
        </div>

        {!! csrf_field() !!}

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
                           value="{{ $product->name or '' }}"
                           required>

                </div>

                <div class="col-md-6">

                    <label for="price">Unit price:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">&euro;</span>
                        </div>
                        <input type="text" class="form-control" id="price" name="price"
                               placeholder="0" value="{{ $product->price or '' }}" required>
                    </div>

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-6">

                    <label for="name">Supplier ID:</label>
                    <input type="text" class="form-control" id="supplier_id" name="supplier_id"
                           placeholder="12345678" value="{{ $product->supplier_id or '' }}">

                </div>

                <div class="col-md-6">

                    <label for="supplier_collo">
                        Collo size
                        <span data-toggle="tooltip" data-placement="top"
                              title="The amount of units in a package when bought. (e.g.: there are 24 cans in a tray)">(?)</span>
                        :
                    </label>
                    <input type="number" class="form-control" id="supplier_collo" name="supplier_collo"
                           placeholder="0" value="{{ $product->supplier_collo or '' }}">

                </div>

            </div>

            <div class="row mb-3">

                <div class="col-md-4">

                    <label for="stock">Current stock:</label>
                    <input type="number" class="form-control" id="stock" name="stock"
                           placeholder="0" value="{{ $product->stock or '' }}">

                </div>

                <div class="col-md-4">

                    <label for="preferred_stock">Preferred stock:</label>
                    <input type="number" class="form-control" id="preferred_stock" name="preferred_stock"
                           placeholder="0" value="{{ $product->preferred_stock or '' }}">

                </div>

                <div class="col-md-4">

                    <label for="max_stock">Maximum stock:</label>
                    <input type="number" class="form-control" id="max_stock" name="max_stock"
                           placeholder="0" value="{{ $product->max_stock or '' }}">

                </div>

            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox"
                           name="is_visible" {{ ($product != null && $product->is_visible ? 'checked' : '') }}>
                    Visible in OmNomCom.
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox"
                           name="is_visible_when_no_stock" {{ ($product != null && $product->is_visible_when_no_stock ? 'checked' : '') }}>
                    Visible in OmNomCom even when out of stock.
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox"
                           name="is_alcoholic" {{ ($product != null && $product->is_alcoholic ? 'checked' : '') }}>
                    Product contains alcohol.
                </label>
            </div>

            <hr>

            <div class="row">

                <div class="col-md-6">

                    <label for="max_stock">Product categories:</label>

                    <select multiple name="product_categories[]" class="form-control">

                        @foreach($categories as $catogory)

                            <option value="{{ $catogory->id }}" {{ ($product != null && $product->categories->contains($catogory) ? 'selected' : '') }}>
                                {{ $catogory->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6">

                    <label for="max_stock">Financial account:</label>

                    <select name="account_id" class="form-control" required>

                        @foreach($accounts as $account)

                            <option value="{{ $account->id }}" {{ ($product != null && $account->id == $product->account_id ? 'selected' : '') }}>
                                {{ $account->name }} ({{ $account->account_number }})
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <hr>

            <div class="custom-file">
                <input type="file" class="custom-file-input" id="image" name="image">
                <label class="custom-file-label" for="customFile">Update product image</label>
            </div>

        </div>

        <div class="card-footer clearfix">


            @if($product)
                <a class="btn btn-danger"
                   onclick="return confirm('Remove product \'{{ $product->name }}\'?');"
                   href="{{ route('omnomcom::products::delete', ['id' => $product->id]) }}">
                    Delete
                </a>
            @endif

            <button type="submit" class="btn btn-success float-right ml-3">Submit</button>

            <a href="{{ route("omnomcom::products::list") }}" class="btn btn-default float-right">Cancel</a>

            @if ($product->ticket)
                <a href="{{ route('tickets::edit', ['id' => $product->ticket->id]) }}" class="btn btn-default float-right">
                    Go to event ticket
                </a>
            @endif

        </div>

    </form>

</div>