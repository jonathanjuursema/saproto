@extends('website.layouts.redesign.dashboard')

@section('page-title')
    OmNomCom Product Administration
@endsection

@section('container')

    <div class="row">

        <div class="col-md-3">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    Products
                </div>

                <div class="card-header">

                    <form id="product-search-form" method="get" action="{{ route("omnomcom::products::index") }}">

                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" name="search">
                            <button type="submit" class="input-group-text" id="basic-addon2">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                    </form>

                </div>

                <div class="card-body">

                    <a href="{{ route('omnomcom::products::create') }}" class="btn btn-success btn-block mb-3">
                        Create a new product
                    </a>

                    <a href="{{ route('omnomcom::products::export_csv') }}" class="btn btn-success btn-block">
                        Export products as CSV
                    </a>

                </div>

            </div>

            <form method="post" action="{{ route("omnomcom::products::bulkupdate") }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        Update the stock
                    </div>

                    <div class="card-body">

                        <p class="card-text">
                            Bulk update stock by inserting a comma-seperated list of product_id's and the amount you
                            want to add.
                        </p>

                        <textarea name="update" class="form-control w-100" rows="7"></textarea>

                    </div>

                    <div class="card-footer">
                        <input type="submit" class="btn btn-success btn-block" value="Update">
                    </div>

                </div>

            </form>
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    Manual stock modifications
                </div>
                <div class="card-body">

                    <p class="card-text">
                        See every manual modification of the Omnomcom stock and create reports.
                    </p>

                </div>

                <div class="card-footer">
                    <a href="{{route('omnomcom::products::mutations')}}" class="btn btn-success btn-block">
                        Mutations
                    </a>
                </div>

            </div>
        </div>

        <div class="col-md-9">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    Product overview
                </div>

                @if (count($products) > 0)

                    <div class="table-responsive">
                        <table class="table table-hover table-sm">

                            <thead>

                            <tr class="bg-dark text-white">

                                <td>Name</td>
                                <td>Price</td>
                                <td>Calories</td>
                                <td>Stock</td>
                                <td>Visible</td>
                                <td>Alcoholic</td>
                                <td></td>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach($products as $product)

                                <tr>

                                    <td>{{ $product->name }} <span class="text-muted">(#{{ $product->id }})</span></td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->calories }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>{{ $product->is_visible ? 'Yes' : 'No' }}</td>
                                    <td>{{ $product->is_alcoholic ? 'Yes' : 'No' }}</td>
                                    <td style="min-width: 60px">
                                        <a href="{{ route('omnomcom::products::edit', ['id' => $product->id]) }}">
                                            <i class="fas fa-edit me-2"></i>
                                        </a>
                                        @include('components.modals.confirm-modal', [
                                            'action' => route('omnomcom::products::delete', ['id' => $product->id]),
                                            'text' => '<i class="fas fa-trash text-danger"></i>',
                                            'title' => 'Confirm Delete',
                                            'message' => "Are you sure you want to delete $product->name?",
                                            'confirm' => 'Delete',
                                        ])
                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>
                    </div>

                    @if(method_exists($products, 'links'))
                        <div class="card-footer pb-0">
                            {!! $products->links() !!}
                        </div>
                    @endif

                @else

                    <div class="card-body">
                        <p class="card-text text-center">
                            There are no products matching your query.
                        </p>
                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection
