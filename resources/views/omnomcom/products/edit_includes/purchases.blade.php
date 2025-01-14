<div class="card">
    <div class="card-header bg-dark text-white">
        Recent orders for this product
    </div>

    @if ($product->orderlines->count() > 0)
        <table class="table table-hover table-sm">
            <tbody>
                @foreach ($orderlines as $orderline)
                    <tr>
                        <td>{{ $orderline->created_at }}</td>
                        <td>
                            @if ($orderline->user)
                                @if ($orderline->user->isMember)
                                    <a
                                        href="{{ route('user::profile', ['id' => $orderline->user->getPublicId()]) }}"
                                    >
                                        {{ $orderline->user->name }}
                                    </a>
                                @else
                                    {{ $orderline->user->name }}
                                @endif
                            @elseif ($orderline->cashier)
                                [Cashier: {{ $orderline->cashier->name }}]
                            @else
                                <em class="text-muted">Anonymised</em>
                            @endif

                            @if ($orderline->description)
                                <p>
                                    <br />
                                    {{ $orderline->description }}
                                </p>
                            @endif
                        </td>
                        <td>{{ $orderline->units }}x</td>
                        <td>
                            &euro;
                            {{ number_format($orderline->total_price, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="card-footer pb-0">
            {!! $orderlines->render() !!}
        </div>
    @else
        <div class="card-body">
            <p class="card-text text-center">
                There are no orders for this product.
            </p>
        </div>
    @endif
</div>
