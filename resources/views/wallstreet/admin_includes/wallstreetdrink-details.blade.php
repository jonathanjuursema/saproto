<form
    method="post"
    action="{{ ! isset($currentDrink) ? route('wallstreet::store') : route('wallstreet::update', ['id' => $currentDrink->id]) }}"
    enctype="multipart/form-data"
>
    @csrf

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            {{ ! isset($currentDrink) ? 'Create WallstreetDrink' : 'Edit WallstreetDrink' }}
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Left column -->
                <div class="col-md-6">
                    <!-- Start -->
                    @include(
                        'components.forms.datetimepicker',
                        [
                            'name' => 'start_time',
                            'label' => 'Opens at:',
                            'placeholder' => $currentDrink ? $currentDrink->start_time : null,
                            'input_class_name' => 'mb-3',
                        ]
                    )

                    <!-- Price decrease per Minute -->
                    <div class="col-md-12 mb-3">
                        <label for="price_decrease">
                            € decrease per minute:
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            class="form-control"
                            id="price_decrease"
                            name="price_decrease"
                            placeholder="0"
                            min="0"
                            value="{{ $currentDrink->price_decrease ?? '' }}"
                            required
                        />
                    </div>

                    <!-- minimum_price -->
                    <div class="col-md-12 mb-3">
                        <label for="minimum_price">€ minimum price:</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            id="minimum_price"
                            name="minimum_price"
                            placeholder="0"
                            value="{{ $currentDrink->minimum_price ?? '' }}"
                            required
                        />
                    </div>
                </div>

                <!-- Right column -->
                <div class="col-md-6">
                    <!-- End -->
                    @include(
                        'components.forms.datetimepicker',
                        [
                            'name' => 'end_time',
                            'label' => 'Closes at:',
                            'placeholder' => $currentDrink ? $currentDrink->end_time : null,
                            'input_class_name' => 'mb-3',
                        ]
                    )

                    <!-- increase per sold item -->
                    <div class="col-md-12 mb-3">
                        <label for="price_increase">
                            € increase per sold item:
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            id="price_increase"
                            name="price_increase"
                            placeholder="0"
                            value="{{ $currentDrink->price_increase ?? '' }}"
                            required
                        />
                    </div>

                    <!-- The chance a random event happens per minute -->
                    <div class="col-md-12 mb-3">
                        <label for="random_events_chance">
                            Random event chance/min:
                        </label>
                        <i
                            class="fas fa-info-circle"
                            data-bs-toggle="tooltip"
                            data-bs-placement="right"
                            title="The chance a random event happens per minute. For instance 30 means a random event happens approximately every half hour. Set to 0 for no random events"
                        ></i>
                        <input
                            type="number"
                            step="1"
                            class="form-control"
                            id="random_events_chance"
                            min="0"
                            name="random_events_chance"
                            placeholder="0"
                            value="{{ $currentDrink->random_events_chance ?? '' }}"
                            required
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Submit</button>

            @if ($currentDrink)
                @include(
                    'components.modals.confirm-modal',
                    [
                        'action' => route('wallstreet::delete', ['id' => $currentDrink->id]),
                        'text' => 'Delete',
                        'title' => 'Confirm Delete',
                        'classes' => 'btn btn-danger ms-2',
                        'message' =>
                            'Are you sure you want to remove this wallstreet drink?<br><br> This will also delete all price history!',
                        'confirm' => 'Delete',
                    ]
                )
            @endif
        </div>
    </div>
</form>
