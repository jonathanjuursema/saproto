<div class="card mb-3">

    <div class="card-header bg-dark text-white mb-1">
        Dinnerform overview
    </div>

    <div class="table-responsive">

        <table class="table table-sm">

            <thead>
                <tr class="bg-dark text-white">
                    <th></th>
                    <th>Restaurant</th>
                    <th>Event</th>
                    <th class="text-center">Status</th>
                    <th>Start</th>
                    <th>End</th>
                    <th class="text-center">Admin</th>
                    <th class="text-center">Controls</th>
                </tr>
            </thead>

            <tbody>
                @if(count($dinnerformList) > 0)
                    @foreach($dinnerformList as $dinnerform)
                        <tr class="align-middle">
                            <td class="text-muted">#{{ $dinnerform->id }}</td>
                            <td>{{ $dinnerform->restaurant }}</td>
                            <td>
                                @isset($dinnerform->event)
                                    <a href="{{ route('event::show', ['id' => $dinnerform->event->getPublicId()]) }}">{{ $dinnerform->event->title}}</a>
                                @endisset
                            </td>
                            <td class="text-center">
                                @if($dinnerform->isCurrent())
                                    <i class="far fa-clock text-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Open"></i>
                                @elseif($dinnerform->closed)
                                    <i class="fas fa-check text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Processed"></i>
                                @else
                                    <i class="fas fa-ban text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Closed"></i>
                                @endif
                            </td>
                            <td>{{ $dinnerform->start->format('Y m-d H:i') }}</td>
                            <td>{{ $dinnerform->end->format('Y m-d H:i') }}</td>
                            <td class="text-center">
                                <a class="btn btn-info badge" href="{{ route('dinnerform::admin', ['id' => $dinnerform->id]) }}">
                                    View orders
                                </a>
                            </td>
                            <td class="text-center">
                                @if(!$dinnerform->closed)
                                    @if($dinnerform->isCurrent())
                                        @include('website.layouts.macros.confirm-modal', [
                                            'action' => route("dinnerform::close", ['id' => $dinnerform->id]),
                                            'text' => '<i class="fas fa-ban text-warning me-2"></i>',
                                            'title' => 'Confirm Close',
                                            'message' => "Are you sure you want to close the dinnerform for $dinnerform->restaurant early? The dinnerform will close automatically at $dinnerform->end.",
                                            'confirm' => 'Close',
                                        ])
                                    @endif
                                    <a href="{{ route('dinnerform::edit', ['id' => $dinnerform->id]) }}">
                                        <i class="fas fa-edit me-2"></i>
                                    </a>
                                    @include('website.layouts.macros.confirm-modal', [
                                        'action' => route("dinnerform::delete", ['id' => $dinnerform->id]),
                                        'text' => '<i class="fas fa-trash text-danger"></i>',
                                        'title' => 'Confirm Delete',
                                        'message' => "Are you sure you want to remove the dinnerform opening $dinnerform->start ordering at $dinnerform->restaurant?<br><br> This will also delete all orderlines!",
                                        'confirm' => 'Delete',
                                    ])
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>There are no dinnerforms available.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
            </tbody>

        </table>

    </div>

</div>

