<form method="post"
      action="{{ ( ! isset($currentDrink) ? route("wallstreet::add") : route("wallstreet::edit", ['id' => $currentDrink->id])) }}"
      enctype="multipart/form-data">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            {{ ! isset($currentDrink) ? 'Create WallstreetDrink' : "Edit WallstreetDrink" }}
        </div>

        <div class="card-body">

            <div class="row">

                <!-- Left column -->
                <div class="col-md-6">
                    <!-- Start -->
                    @include('components.forms.datetimepicker', [
                        'name' => 'start_time',
                        'label' => 'Opens at:',
                        'placeholder' => $currentDrink? $currentDrink->start_time:null,
                        'input_class_name' => 'mb-3'
                    ])

                    <!-- Price decrease per Minute -->
                    <div class="col-md-12 mb-3">
                        <label for="price_decrease">€ decrease per minute:</label>
                        <input type="number" step="0.01" class="form-control" id="price_decrease" name="price_decrease"
                               placeholder='0'
                               value="{{ $currentDrink->price_decrease ?? ''}}"
                               required
                        />
                    </div>

                    <!-- minimum_price -->
                    <div class="col-md-12 mb-3">
                        <label for="minimum_price">€ minimum price:</label>
                        <input type="number" step="0.01" class="form-control" id="minimum_price" name="minimum_price"
                               placeholder='0'
                               value="{{ $currentDrink->minimum_price ?? ''}}"
                               required
                        />
                    </div>

                </div>

                <!-- Right column -->
                <div class="col-md-6">

                    <!-- End -->
                    @include('components.forms.datetimepicker',[
                        'name' => 'end_time',
                        'label' => 'Closes at:',
                        'placeholder' => $currentDrink ? $currentDrink->end_time : null,
                        'input_class_name' => 'mb-3'
                    ])


                    <!-- Regular Discount -->
                    <div class="col-md-12 mb-3">
                        <label for="price_increase">€ increase per sold item:</label>
                        <input type="number" step="0.01" class="form-control" id="price_increase" name="price_increase"
                               placeholder='0'
                               value="{{ $currentDrink->price_increase ?? '' }}"
                               required
                        />
                    </div>

                    <!-- Random Events -->
                    <div class="col-md-12 mb-3">
                        @include('components.forms.checkbox', [
                            'name' => 'random_events',
                            'checked' => $currentDrink ? $currentDrink->random_events : true,
                            'label' => 'Random events <i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" title="If random events happen during the drink"></i>'
                        ])
                    </div>

                </div>

            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Submit</button>

            @if($currentDrink)
                @include('components.modals.confirm-modal', [
                                           'action' => route("wallstreet::delete", ['id' => $currentDrink->id]),
                                           'text' => 'Delete',
                                           'title' => 'Confirm Delete',
                                           'classes' => 'btn btn-danger ms-2',
                                           'message' => "Are you sure you want to remove this wallstreet drink?<br><br> This will also delete all price history!",
                                           'confirm' => 'Delete',
                                       ])
            @endif
        </div>

    </div>

</form>