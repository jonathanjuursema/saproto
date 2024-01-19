<div class="card mb-3">

    <div class="card-header bg-dark mb-1 justify-content-between d-inline-flex">
        <div class="text-white">WallstreetDrink events overview</div>
    </div>

    @if(count($allEvents) > 0)
        <div class="table-responsive">

            <table class="table table-sm">

                <thead>
                <tr class="bg-dark text-white">

                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Percentage</th>
                    <th>Controls</th>
                </tr>
                </thead>

                <tbody>
                @foreach($allEvents as $wallstreetEvent)
                    <tr class="align-middle text-nowrap">
                        <td class="text-muted">#{{ $wallstreetEvent->id }}</td>
                        <td class="text">{{ $wallstreetEvent->name }}</td>
                        <td class="text text-truncate">{{ $wallstreetEvent->description }}</td>
                        <td class="text">%{{ $wallstreetEvent->percentage }}</td>


                        <td>
                            <a href="{{ route('wallstreet::events::edit', ['id' => $wallstreetEvent->id]) }}">
                                <i class="fas fa-edit me-4"></i>
                            </a>
                            @include('components.modals.confirm-modal', [
                                'action' => route("wallstreet::delete", ['id' => $wallstreetEvent->id]),
                                'text' => '<i class="fas fa-trash text-danger"></i>',
                                'title' => 'Confirm Delete',
                                'message' => "Are you sure you want to remove this wallstreet drink?<br><br> This will also delete all price history!",
                                'confirm' => 'Delete',
                            ])
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

