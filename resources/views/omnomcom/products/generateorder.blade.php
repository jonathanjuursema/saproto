@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Generate Supplier Order
@endsection

@section('container')
    @if (count($orders) > 0)
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">@yield('page-title')</div>

                    <div class="card-body">
                        <a
                            href="?csv"
                            class="btn btn-success btn-block mb-3"
                            target="_blank"
                        >
                            Supplier CSV
                        </a>

                        <p class="card-text">
                            This table shows how much of each products needs to
                            be ordered to reach the preferred stock as set for
                            that product. This list
                            <strong>only</strong>
                            includes products that are set to 'be in stock by
                            default' and that need to be re-ordered to have
                            sufficient stock. This is determined by the
                            <i>
                                Visible in the OmNomCom even when out of stock
                            </i>
                            checkbox in the product's settings. To include or
                            exclude products from this list, head over to that
                            product's page and change the setting.
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-hover table-borderless table-sm"
                        >
                            <thead>
                                <tr class="text-nowrap">
                                    <th></th>
                                    <th>Product Name</th>
                                    <th>Supplier ID</th>
                                    <th>Collo</th>
                                    <th>Stock</th>
                                    <th>Target</th>
                                    <th>&nbsp;</th>
                                    <th colspan="2">Collo to Order</th>
                                    <th colspan="2">Stock After Order</th>
                                </tr>
                            </thead>

                            @foreach ($orders as $order)
                                <tr>
                                    <td class="text-end">
                                        {{ $order->product->id }}
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('omnomcom::products::edit', ['id' => $order->product->id]) }}"
                                        >
                                            {{ $order->product->name }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $order->product->supplier_id }}
                                    </td>
                                    @if ($order->order_collo > 0)
                                        <td class="opacity-50">
                                            {{ $order->product->supplier_collo > 0 ? $order->product->supplier_collo : null }}
                                        </td>
                                        <td>{{ $order->product->stock }}</td>
                                        <td>
                                            {{ $order->product->preferred_stock }}
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <strong>
                                                {{ $order->order_collo }}
                                            </strong>
                                        </td>
                                        <td class="opacity-50">
                                            {{ $order->order_products }} units
                                        </td>
                                        <td>{{ $order->new_stock }}</td>
                                        <td class="opacity-50">
                                            + {{ $order->new_surplus }}
                                        </td>
                                    @else
                                        <td colspan="8" class="opacity-50">
                                            No need to order.
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <p class="text-center">No products need to be reordered.</p>
    @endif
@endsection
