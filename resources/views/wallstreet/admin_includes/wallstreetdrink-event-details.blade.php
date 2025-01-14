<form
    method="post"
    action="{{ ! isset($currentEvent) ? route('wallstreet::events::store') : route('wallstreet::events::update', ['id' => $currentEvent->id]) }}"
    enctype="multipart/form-data"
>
    @csrf

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            {{ ! isset($currentEvent) ? 'Create WallstreetDrink Event' : 'Edit WallstreetDrink Event' }}
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Left column -->
                <div class="col-md-6">
                    <!-- percentage that the products will be affected -->
                    <div class="col-md-12 mb-3">
                        <label for="name">Event name:</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="title"
                            placeholder="A keg fell of the truck!"
                            value="{{ old('title', $currentEvent->name ?? '') }}"
                            required
                        />
                    </div>
                </div>
                <!-- Right column -->
                <div class="col-md-6">
                    <label for="percentage">
                        Percentage price difference
                        <i
                            class="fas fa-info-circle"
                            data-bs-toggle="tooltip"
                            data-bs-placement="right"
                            title="The percentage of the current price that will be added. For instance: a weizen is €5. This field is -10(%). The new price will be €4.50"
                        ></i>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        class="form-control"
                        id="percentage"
                        name="percentage"
                        placeholder="-10"
                        value="{{ $currentEvent->percentage ?? '' }}"
                        required
                    />
                </div>
            </div>
            <div class="form-group">
                <!-- Image -->
                <div class="custom-file">
                    @if ($currentEvent?->image == null)
                        <label for="image">Set event image:</label>
                    @else
                        <image
                            class="mb-3"
                            src="{{ $currentEvent->image->generatePath() ?? '' }}"
                        />
                    @endif
                    <input
                        type="file"
                        id="image"
                        class="form-control"
                        name="image"
                    />
                </div>
                <label for="editor">Description</label>
                @include(
                    'components.forms.markdownfield',
                    [
                        'name' => 'description',
                        'placeholder' =>
                            $currentEvent == null
                                ? 'A keg fell of the Grolsch truck and now we have to empty it! What a pity...'
                                : null,
                        'value' => old(
                            'description',
                            $currentEvent == null ? null : $currentEvent->description,
                        ),
                    ]
                )
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Submit</button>

            @if ($currentEvent)
                @include(
                    'components.modals.confirm-modal',
                    [
                        'action' => route('wallstreet::events::delete', [
                            'id' => $currentEvent->id,
                        ]),
                        'text' => 'Delete',
                        'title' => 'Confirm Delete',
                        'classes' => 'btn btn-danger ms-2',
                        'message' => 'Are you sure you want to remove this wallstreet event?',
                        'confirm' => 'Delete',
                    ]
                )
            @endif
        </div>
    </div>
</form>
