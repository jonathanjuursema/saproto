@extends('website.layouts.redesign.dashboard')

@section('page-title')
    OmNomCom Account Administration
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-2">

            <form method="post" action="{{ route('omnomcom::accounts::aggregate', ['account' => $account->id]) }}">

                @csrf

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        {{ $account->name }}
                    </div>

                    <div class="card-body">

                        <p class="card-text text-center">
                            Account number: <strong>{{ $account->account_number }}</strong>
                        </p>

                    </div>

                </div>

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        Aggregated sales overview
                    </div>

                    <div class="card-body">

                        @include('components.forms.datetimepicker', [
                            'name' => 'start',
                            'label' => 'Start'
                        ])

                        @include('components.forms.datetimepicker', [
                            'name' => 'end',
                            'label' => 'End'
                        ])

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">Generate</button>
                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-4">

            <div class="card mb-3">

                <div class="card-header bg-dark text-white mb-1">
                    Account products
                </div>

                <div class="card-body pb-0">

                    @if ($products->count() > 0)

                        <li class="list-group">
                            @foreach($products as $product)
                                <a href="{{ route("omnomcom::products::edit",['id' => $product->id]) }}"
                                   class="list-group-item">
                                    {{ $product->name }}
                                </a>
                            @endforeach
                        </li>

                        <hr>

                        {!! $products->links() !!}

                    @else
                        <p class="card-text text-center">
                            There are no products for this account.
                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection
