@extends('website.layouts.redesign.generic')

@section('page-title')
    Orderline wizard
@endsection

@section('container')
    <div class="row">
        <div class="column">

            <form method="post" action="{{ route('omnomcom::orders::storebulk') }}">

                {!! csrf_field() !!}

                <div class="card mb-3">

                    <div class="card-header d-inline-flex justify-content-between">
                        <h5>Add orderlines</h5>
                        <a href="{{ route('omnomcom::orders::adminlist') }}" class="btn btn-default">
                            Back to Orderline Overview
                        </a>
                    </div>

                    <div id="orderline-rows" class="card-body">

                        <div class="row orderline-row">

                            <div class="col-lg-3">

                                <select name="user[]" class="form-control orderline-user">
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} (#{{ $member->id }})
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-lg-3">

                                <select name="product[]" class="form-control orderline-product">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-price="{{ $product->price }}">{{ $product->name }}
                                            (&euro;{{ $product->price }}, #{{ $product->id }})
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-lg-2">

                                <div class="input-group mb-3">
                                    <input type="number" class="form-control orderline-units" name="units[]" value="1">
                                    <span class="input-group-text">x</span>
                                </div>

                            </div>

                            <div class="col-lg-2">

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">&euro;</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control orderline-price" name="price[]"
                                           placeholder="0.00">
                                </div>

                            </div>

                            <div class="col-lg-2">

                                <button type="button" class="btn btn-danger w-100 orderline-delete-row" disabled>
                                    <i class="fas fa-minus-circle"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-2 offset-md-8 text-end">Total price:</div>
                            <div class="col-md-2" id="total-price">&euro; 0.00</div>
                        </div>
                    </div>

                    <div class="modal-footer">

                        <div class="col-7">
                            <input type="text" class="form-control" name="description"
                                   placeholder="Optional additional information.">
                        </div>
                        <div class="col-2">
                            <button id="orderline-add-row" class="btn btn-outline-success btn-block">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                        <div class="col-1">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>

                    </div>

                </div>

            </form>
        </div>
    </div>
@endsection

@push('javascript')

    <script type="text/javascript" nonce="{{ csp_nonce() }}">

        document.getElementById('orderline-add-row').addEventListener('click', e => {
            e.preventDefault();

            const prevRow = Array.from(document.getElementsByClassName('orderline-row')).pop();
            const newRow = prevRow.cloneNode(true);
            document.getElementById('orderline-rows').append(newRow);

            const deleteBtn = newRow.querySelector('.orderline-delete-row');
            deleteBtn.addEventListener('click', e => {
                newRow.remove();
                calculateTotalPrice();
            });
            deleteBtn.disabled = false;

            calculateTotalPrice();
        });

        document.getElementById('orderline-modal').addEventListener('change', _ => {
            calculateTotalPrice();
        });

        function calculateTotalPrice() {
            const rows = Array.from(document.getElementsByClassName('orderline-row'));
            const totalPrice = rows.reduce((total, el) => {
                let currentPrice;
                const product = el.querySelector('.orderline-product option:checked');
                const productPrice = el.querySelector('.orderline-price');
                const units = el.querySelector('.orderline-units').value;
                if (productPrice.value === '') currentPrice = product.getAttribute('data-price');
                else currentPrice = productPrice.value;
                return total + currentPrice * units;
            }, 0);
            document.getElementById('total-price').innerHTML = '&euro; ' + totalPrice.toFixed(2);
        }

        window.addEventListener('load', _ => {
            calculateTotalPrice();
        });

    </script>

@endpush
