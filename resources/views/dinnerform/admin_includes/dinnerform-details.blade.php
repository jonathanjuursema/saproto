<form method="post"
      action="{{ ( ! isset($dinnerformCurrent) ? route("dinnerform::store") : route("dinnerform::update", ['id' => $dinnerformCurrent->id])) }}"
      enctype="multipart/form-data">

    @csrf

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            {{ ! isset($dinnerformCurrent) ? 'Create Dinnerform' : "Edit Dinnerform at $dinnerformCurrent->restaurant" }}
        </div>

        <div class="card-body">

            <div class="row">

                <!-- Left column -->
                <div class="col-md-6">

                    <!-- Restaurant -->
                    <div class="col-md-12 mb-3">
                        <label for="restaurant">Dinnerform restaurant:</label>
                        <input type="text" class="form-control" id="restaurant" name="restaurant"
                               placeholder="Elat Roma"
                               value="{{ $dinnerformCurrent->restaurant ?? '' }}"
                               required
                        />
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label for="description">Description:</label>
                        <input type="text" class="form-control" id="description" name="description"
                               placeholder="Order with us at Elat Roma"
                               value="{{ $dinnerformCurrent->description ?? '' }}"
                               required
                        />
                    </div>

                    <!-- Website -->
                    <div class="col-md-12 mb-3">
                        <label for="url">Restaurant website:</label>
                        <input type="url" class="form-control" id="url" name="url"
                               placeholder='www.elat-roma.nl/'
                               value="{{ $dinnerformCurrent->url ?? ''}}"
                               required
                        />
                    </div>

                    <!-- Helper Discount -->
                    <div class="col-md-12 mb-3">
                        <label for="helper-discount">Helper discount €:</label>
                        <input type="number" step="0.01" class="form-control" id="helper-discount"
                               name="helper_discount"
                               placeholder='7.5'
                               value="{{ $dinnerformCurrent->helper_discount ?? ''}}"
                               required
                        />
                    </div>

                    <!-- Ordered by -->
                    <div class="col-md-12 mb-3">
                        <div class="form-group autocomplete">
                            <label for="ordered_by">Ordered by:</label>
                            <input class="form-control user-search" id="ordered_by"
                                   value="{{ $dinnerformCurrent->ordered_by ?? ''}}" name="ordered_by"
                                   data-label="User:" required>
                        </div>
                    </div>
                </div>

                <!-- Right column -->
                <div class="col-md-6">

                    <!-- Start -->
                    @include('components.forms.datetimepicker', [
                        'name' => 'start',
                        'label' => 'Opens at:',
                        'placeholder' => $dinnerformCurrent ? $dinnerformCurrent->start->timestamp : null,
                        'input_class_name' => 'mb-3'
                    ])

                    <!-- End -->
                    @include('components.forms.datetimepicker',[
                        'name' => 'end',
                        'label' => 'Closes at:',
                        'placeholder' => $dinnerformCurrent ? $dinnerformCurrent->end->timestamp : null,
                        'input_class_name' => 'mb-3'
                    ])

                    <!-- Event -->
                    <div class="row align-items-end mb-6">
                        <div class="col-md-12 mb-3 form-group autocomplete">
                            <label for="event-select">Event:</label>
                            <input class="form-control event-search" id="event-select" name="event_select"
                                   value="{{ $dinnerformCurrent ? $dinnerformCurrent->event_id : '' }}"
                                   placeholder="{{ ($dinnerformCurrent?->event && $dinnerformCurrent->event->activity) ? $dinnerformCurrent->event->title : '' }}"
                            />
                        </div>
                    </div>

                    <!-- Regular Discount -->
                    <div class="col-md-12 mb-3">
                        <label for="regular-discount">Regular discount %:</label>
                        <input type="number" step="0.01" class="form-control" id="regular-discount"
                               name="regular_discount"
                               placeholder='0'
                               value="{{ $dinnerformCurrent->regular_discount_percentage ?? ''}}"
                               required
                        />
                    </div>

                    <!-- Homepage -->
                    <div class="col-md-12 mb-3 mt-5">
                        @include('components.forms.checkbox', [
                            'name' => 'homepage',
                            'checked' => $dinnerformCurrent?->visible_home_page,
                            'label' => 'Visible on the homepage?'
                        ])
                    </div>

                </div>

            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Submit</button>

            @if($dinnerformCurrent)

                @include('components.modals.confirm-modal', [
                                           'action' => route("dinnerform::delete", ['id' => $dinnerformCurrent->id]),
                                           'text' => 'Delete',
                                           'title' => 'Confirm Delete',
                                           'classes' => 'btn btn-danger ms-2',
                                           'message' => "Are you sure you want to remove the dinnerform opening $dinnerformCurrent->start ordering at $dinnerformCurrent->restaurant?<br><br> This will also delete all orderlines!",
                                           'confirm' => 'Delete',
                                       ])

            @endif
        </div>

    </div>

</form>
