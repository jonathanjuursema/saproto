<form method="post"
      action="{{ ($event == null ? route("event::add") : route("event::edit", ['id' => $event->id])) }}"
      enctype="multipart/form-data">

    {!! csrf_field() !!}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            <div class="p-1 m-1 fw-bold d-inline-block">Event details</div>

            @include('event.edit_includes.buttonbar')
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-7">

                    <div class="row align-items-end mb-3">

                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="name">Event name:</label>
                            <input type="text" class="form-control" id="name" name="title"
                                   placeholder="Lightsaber Building in the SmartXp"
                                   value="{{ old('title',$event->title ?? '') }}"
                                   required>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6 mb-3">
                            <label for="location">Location:</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   placeholder="SmartXp" value="{{ old('location',$event->location ?? '') }}" required>
                        </div>

                        <!-- Start -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.datetimepicker',[
                                'name' => 'start',
                                'label' => 'Event start:',
                                'placeholder' => request()->old('start') ? strtotime(request()->old('start')) : ($event ? $event->start : null)
                            ])
                        </div>

                        <!-- End -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.datetimepicker',[
                                'name' => 'end',
                                'label' => 'Event end:',
                                'placeholder' => request()->old('start')?strtotime(request()->old('end')):($event ? $event->end : null)
                            ])
                        </div>

                        <!-- Visibility -->
                        <div class="col-md-6 mb-3">
                            <label for="secret">Event visibility:</label>
                            <select id="secret" name="secret" class="form-control" required>
                                <option value="1" @selected(old('secret') === 1 || $event != null && $event->secret)>
                                    Secret
                                </option>
                                <option value="0" @selected(old('secret') === 0 || $event != null && !$event->secret)>
                                    Public
                                </option>
                            </select>
                        </div>

                        <!-- Publication -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.datetimepicker',[
                                'name' => 'publication',
                                'label' => 'Publication time: <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" data-bs-placement="right" title="By setting this the event visibility will be ignored until the specified time, then it will be made public"></i>',
                                'placeholder' => old('publication') ? strtotime(old('publication')) : ($event ? $event->publication : null),
                                'not_required'=> true,
                            ])
                        </div>

                        <!-- Organisers -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group autocomplete">
                                <label for="organisation">Organised by:</label>
                                <input class="form-control committee-search" id="organisation" name="committee"
                                       placeholder="{{ $event->committee->name ?? '' }}"
                                       value="{{ $event->committee->id ?? '' }}">
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="col-md-6 mb-3">
                            <div class="custom-file">
                                <label for="image">Set event image:</label>
                                <input type="file" id="image" class="form-control" name="image">
                            </div>
                        </div>

                        <!-- Category -->
                        @php($categories = Proto\Models\EventCategory::all())
                        @if(count($categories) > 0)
                            <div class="col-md-6 mb-3">

                                <label for="category">Event category:</label>
                                <select id="category" name="category" class="form-control">
                                    <option @selected($event && !$event->category)>
                                        Uncategorized
                                    </option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected($event && $event->category_id == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- External -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.checkbox', [
                                'name' => 'is_external',
                                'checked' => $event?->is_external,
                                'label' => 'This activity is not organized by Proto.'
                            ])
                        </div>

                        <!-- Food -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.checkbox', [
                                'name' => 'involves_food',
                                'checked' => $event?->involves_food,
                                'label' => 'This activity involves people eating food.'
                            ])
                        </div>

                        <!-- Force Calendar -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.checkbox', [
                                'name' => 'force_calendar_sync',
                                'checked' => $event?->force_calendar_sync,
                                'label' => 'Always sync this event to user calendars. <i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="right" title="This will also sync this event to the calendars of users that specifically opted to only sync events they are either attending, organizing or helping at. This feature should only be used for events like GMMs."></i>'
                            ])
                        </div>

                        <!-- Feature -->
                        <div class="col-md-6 mb-3">
                            @include('components.forms.checkbox', [
                                'name' => 'is_featured',
                                'checked' => $event?->is_featured,
                                'label' => 'Feature this event on the homepage.'
                            ])
                        </div>

                    </div>

                    @if($event && $event->image)

                        <hr>

                        <h5>Current image:</h5>
                        <img src="{!! $event->image->generateImagePath(800,300) !!}" class="w-100 border">

                    @endif

                </div>

                <div class="col-md-5">

                    <div class="form-group">
                        <label for="editor">Description</label>
                        @include('components.forms.markdownfield', [
                            'name' => 'description',
                            'placeholder' => $event == null ? "Please elaborate on why this event is awesome." : null,
                            'value' => old('description',$event == null ? null : $event->description)
                        ])
                    </div>

                    <div class="form-group">
                        <label for="editor-summary">Summary</label>
                        @include('components.forms.markdownfield', [
                            'name' => 'summary',
                            'placeholder' => $event == null ? "A summary (used in the newsletter for example). Only a small description is required, other details will be added." : null,
                            'value' => old('summary',$event == null ? null : $event->summary)
                        ])
                    </div>

                </div>

            </div>

        </div>

        <div class="card-footer">
            @include('event.edit_includes.buttonbar')
        </div>

    </div>

</form>