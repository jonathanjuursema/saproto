<form method="post"
      action="{{ ( ! isset($currentEvent) ? route("wallstreet::events::add") : route("wallstreet::events::edit", ['id' => $currentEvent->id])) }}"
      enctype="multipart/form-data">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            {{ ! isset($currentEvent) ? 'Create WallstreetDrink Event' : "Edit WallstreetDrink Event" }}
        </div>

        <div class="card-body">

            <div class="row">

                <!-- Left column -->
                <div class="col-md-6">


                    <!-- percentage that the products will be affected -->
                    <div class="col-md-12 mb-3">
                        <label for="name">Event name:</label>
                        <input type="text" class="form-control" id="name" name="title"
                               placeholder="A keg fell of the truck!"
                               value="{{ old('title', $currentEvent->name ?? '') }}"
                               required>
                    </div>
                </div>
                <!-- Right column -->
                <div class="col-md-6">
                    <label for="percentage">price % per product</label>
                    <input type="number" step="0.01" class="form-control" id="percentage" name="percentage"
                           placeholder='1'
                           value="{{ $currentEvent->percentage ?? '' }}"
                           required
                    />
                </div>
            </div>
            <div class="form-group">
                <!-- Image -->
                <div class="custom-file">
                    @if($currentEvent?->image == null)
                        <label for="image">Set event image:</label>
                    @else
                        <image class="mb-3" src="{{ $currentEvent->image->generatePath() ?? '' }}"/>
                    @endif
                    <input type="file" id="image" class="form-control" name="image">
                </div>
                <label for="editor">Description</label>
                @include('components.forms.markdownfield', [
                    'name' => 'description',
                    'placeholder' => $currentEvent == null ? "A keg fell of the Grolsch truck and now we have to empty it! What a pity..." : null,
                    'value' => old('description', $currentEvent == null ? null : $currentEvent->description)
                ])

            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Submit</button>

            @if($currentEvent)
                @include('components.modals.confirm-modal', [
                                           'action' => route("wallstreet::delete", ['id' => $currentEvent->id]),
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