@php
    /** @var $order \App\Models\DinnerformOrderline */
@endphp

<div class="card mb-3">
    <div class="card-header bg-dark text-white">Your order:</div>

    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Price</th>
                    @if ($dinnerform->hasDiscount())
                        <th>Discount</th>
                        <th>Total</th>
                    @else
                    @endif
                    @if ($dinnerform->isCurrent())
                        <th></th>
                    @endif
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td style="max-width: 100px">{{ $order->description }}</td>
                    <td>€ {{ number_format($order->price, 2) }}</td>
                    @if ($dinnerform->hasDiscount())
                        <td>
                            - €
                            {{ number_format($order->price - $order->price_with_discount, 2) }}
                        </td>
                        <td>
                            €
                            {{ number_format($order->price_with_discount, 2) }}
                        </td>
                    @endif

                    @if ($dinnerform->isCurrent())
                        <td>
                            @include(
                                'components.modals.confirm-modal',
                                [
                                    'action' => route('dinnerform::orderline::delete', [
                                        'id' => $order->id,
                                    ]),
                                    'text' => '<i class="fas fa-trash text-danger"></i>',
                                    'title' => 'Confirm Delete',
                                    'message' => "Are you sure you want to delete your order: $order->description at $dinnerform->restaurant?",
                                    'confirm' => 'Delete',
                                ]
                            )
                        </td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>
