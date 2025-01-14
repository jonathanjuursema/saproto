@extends('website.layouts.redesign.dashboard')

@section('page-title')
    Dinnerform Admin
@endsection

@section('container')
    <div class="card mb-3 col-lg-8 ms-auto me-auto">
        <div class="card-header bg-dark text-white mb-1">
            <span>
                Dinnerform orderline overview for
                <strong>{{ $dinnerform->restaurant }}</strong>
                ordered on
                <strong>{{ $dinnerform->end->format('Y m-d') }}</strong>
            </span>
            <a
                href="{{ route('dinnerform::create') }}"
                class="btn btn-info badge float-end ms-2"
            >
                <i class="fas fa-hand-point-left me-1"></i>
                Return to overview
            </a>
            @if ($dinnerform->isCurrent())
                @include(
                    'components.modals.confirm-modal',
                    [
                        'action' => route('dinnerform::close', ['id' => $dinnerform->id]),
                        'text' => '<i class="fas fa-ban me-1"></i> Close dinnerform!',
                        'title' => 'Confirm Close',
                        'message' => "Are you sure you want to close the dinnerform for $dinnerform->restaurant early? The dinnerform will close automatically at $dinnerform->end.",
                        'confirm' => 'Close',
                        'classes' => 'btn btn-warning badge float-end',
                    ]
                )
            @elseif (! $dinnerform->closed && Auth::user()->can('finadmin'))
                @include(
                    'components.modals.confirm-modal',
                    [
                        'action' => route('dinnerform::process', ['id' => $dinnerform->id]),
                        'text' => '<i class="fas fa-file-export me-1"></i> Process dinnerform',
                        'title' => 'Confirm processing dinnerform',
                        'message' => "Are you sure you want to process the dinnerform from $dinnerform->restaurant?<br> This will convert all dinnerform orderlines to orderlines and mean none of the orderlines can be changed anymore!",
                        'confirm' => 'Process',
                        'classes' => 'btn btn-danger badge float-end',
                    ]
                )
            @else
                <span
                    class="badge btn bg-primary cursor-default badge float-end"
                >
                    <i class="fas fa-check"></i>
                    Processed!
                </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-sm text-center">
                <thead>
                    <tr class="bg-dark text-white">
                        <th>Orders</th>
                        <th>Helpers</th>
                        <th>Helper discount</th>
                        <th>Regular discount</th>
                        <th>Total Price</th>
                        <th>Total Discounted</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>{{ $dinnerform->orderCount() }}</td>
                        <td>{{ $dinnerform->helperCount() }}</td>
                        <td>
                            €{{ number_format($dinnerform->helper_discount, 2) }}
                        </td>
                        <td>
                            {{ $dinnerform->regular_discount_percentage }}%
                        </td>
                        <td>
                            €{{ number_format($dinnerform->totalAmount(), 2) }}
                        </td>
                        <td>
                            €{{ number_format($dinnerform->totalAmountWithDiscount(), 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            @if (count($orderList) > 0)
                <table class="table table-sm">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th></th>
                            <th>User</th>
                            <th>Helper</th>
                            <th>Order</th>
                            <th>Price</th>
                            <th>Discounted</th>
                            <th class="text-center">Controls</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orderList as $order)
                            <tr>
                                <td class="text-muted">
                                    #{{ $order->user->id }}
                                </td>
                                <td>
                                    {{ $order->user->name }}
                                </td>
                                <td>
                                    @if ($order->helper)
                                        <i
                                            class="fas fa-check text-info"
                                            aria-hidden="true"
                                        ></i>
                                    @endif
                                </td>
                                <td>{{ $order->description }}</td>
                                <td>€{{ number_format($order->price, 2) }}</td>
                                <td>
                                    €{{ number_format($order->price_with_discount, 2) }}
                                </td>
                                <td class="text-center">
                                    @if (! $order->closed)
                                        <a
                                            href="{{ route('dinnerform::orderline::edit', ['id' => $order->id]) }}"
                                        >
                                            <i class="fas fa-edit me-2"></i>
                                        </a>
                                        @include(
                                            'components.modals.confirm-modal',
                                            [
                                                'action' => route('dinnerform::orderline::delete', [
                                                    'id' => $order->id,
                                                ]),
                                                'text' => '<i class="fas fa-trash text-danger"></i>',
                                                'title' => 'Confirm Delete',
                                                'message' => "Are you sure you want to remove the dinnerform opening $dinnerform->start ordering at $dinnerform->restaurant?",
                                                'confirm' => 'Delete',
                                            ]
                                        )
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center text-muted pb-3">
                    There are no orders yet!
                </div>
            @endif
        </div>
    </div>
@endsection
