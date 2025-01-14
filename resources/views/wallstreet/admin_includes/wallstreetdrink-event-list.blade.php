<div class="card mb-3">
    <div class="card-header bg-dark mb-1 justify-content-between d-inline-flex">
        <div class="text-white">WallstreetDrink events overview</div>
    </div>

    @if (count($allEvents) > 0)
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-dark text-white">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Percentage</th>
                        <th>Active</th>
                        <th>Controls</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($allEvents as $wallstreetEvent)
                        <tr class="align-middle text-nowrap">
                            <td class="text-muted">
                                #{{ $wallstreetEvent->id }}
                            </td>
                            <td class="text">{{ $wallstreetEvent->name }}</td>
                            <td class="text">
                                %{{ $wallstreetEvent->percentage }}
                            </td>
                            <td>
                                @include(
                                    'components.forms.checkbox',
                                    [
                                        'name' => 'show_only_active',
                                        'checked' => $wallstreetEvent->active,
                                        'label' => '',
                                        'value' => $wallstreetEvent->id,
                                    ]
                                )
                            </td>

                            <td>
                                <a
                                    href="{{ route('wallstreet::events::edit', ['id' => $wallstreetEvent->id]) }}"
                                >
                                    <i class="fas fa-edit me-4"></i>
                                </a>
                                @include(
                                    'components.modals.confirm-modal',
                                    [
                                        'action' => route('wallstreet::events::delete', [
                                            'id' => $wallstreetEvent->id,
                                        ]),
                                        'text' => '<i class="fas fa-trash text-danger"></i>',
                                        'title' => 'Confirm Delete',
                                        'message' => 'Are you sure you want to remove this wallstreet event?',
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
            There are no wallstreet events yet!
        </div>
    @endif
</div>

@push('javascript')
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        var checkboxes = document.querySelectorAll('input[type=checkbox]')
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', function (event) {
                //disable the checkbox
                event.target.disabled = true
                //send the request
                get(
                    '{{ urldecode(route('api::wallstreet::toggle_event')) }}',
                    { id: event.target.value }
                ).then((response) => {
                    if (response.id != null) {
                        event.target.checked = response.active
                        event.target.disabled = false
                    } else {
                        event.target.checked = !event.target.checked
                        event.target.disabled = false
                    }
                })
            })
        })
    </script>
@endpush
