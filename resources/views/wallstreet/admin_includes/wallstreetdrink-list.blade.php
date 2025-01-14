<div class="card mb-3">
    <div class="card-header bg-dark mb-1 justify-content-between d-inline-flex">
        <div class="text-white">WallstreetDrink overview</div>
        <div>
            <a
                class="btn btn-info badge"
                href="{{ route('wallstreet::marquee') }}"
            >
                View Marquee Screen!
            </a>
        </div>
    </div>

    @if (count($allDrinks) > 0)
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-dark text-white">
                        <th>Id</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Minimum price</th>
                        <th>Decrease</th>
                        <th>Increase/item</th>
                        <th>Events</th>
                        <th class="text-center">Admin</th>
                        <th>Controls</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($allDrinks as $wallstreetDrink)
                        <tr class="align-middle text-nowrap">
                            <td class="text-muted">
                                #{{ $wallstreetDrink->id }}
                            </td>
                            <td>
                                {{ Carbon::createFromTimestamp($wallstreetDrink->start_time)->format('m-d-Y H:i') }}
                            </td>
                            <td>
                                {{ Carbon::createFromTimestamp($wallstreetDrink->end_time)->format('m-d-Y H:i') }}
                            </td>
                            <td>€{{ $wallstreetDrink->minimum_price }}</td>
                            <td>€{{ $wallstreetDrink->price_decrease }}</td>
                            <td>€{{ $wallstreetDrink->price_increase }}</td>
                            <td>
                                @if ($wallstreetDrink->random_events_chance > 0)
                                        1/{{ $wallstreetDrink->random_events_chance }}
                                @endif
                            </td>
                            <td class="text-center px-4">
                                <a
                                    class="btn btn-info badge"
                                    href="{{ route('wallstreet::statistics', ['id' => $wallstreetDrink->id]) }}"
                                >
                                    View Price History
                                </a>
                            </td>

                            <td>
                                @if ($wallstreetDrink->isCurrent())
                                    @include(
                                        'components.modals.confirm-modal',
                                        [
                                            'action' => route('wallstreet::close', ['id' => $wallstreetDrink->id]),
                                            'text' => '<i class="fas fa-ban text-warning me-4"></i>',
                                            'title' => 'Confirm Close',
                                            'message' =>
                                                'Are you sure you want to close this wallstreet drink early? The drink will close automatically at:' .
                                                Carbon::createFromTimestamp($wallstreetDrink->end_time)->format(
                                                    'm-d-Y H:i',
                                                ),
                                            'confirm' => 'Close',
                                        ]
                                    )
                                @endif

                                <a
                                    href="{{ route('wallstreet::edit', ['id' => $wallstreetDrink->id]) }}"
                                >
                                    <i class="fas fa-edit me-4"></i>
                                </a>
                                @include(
                                    'components.modals.confirm-modal',
                                    [
                                        'action' => route('wallstreet::delete', ['id' => $wallstreetDrink->id]),
                                        'text' => '<i class="fas fa-trash text-danger"></i>',
                                        'title' => 'Confirm Delete',
                                        'message' =>
                                            'Are you sure you want to remove this wallstreet drink?<br><br> This will also delete all price history!',
                                        'confirm' => 'Delete',
                                    ]
                                )
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center text-muted py-3">
            There are no wallstreet drinks yet!
        </div>
    @endif
</div>
