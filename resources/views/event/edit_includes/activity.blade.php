<div class="card mb-3">
    <div class="card-header bg-dark text-white">Sign-up details</div>

    @if ($event != null)
        <form
            method="post"
            action="{{ route('event::addsignup', ['id' => $event->id]) }}"
        >
            @csrf

            <div class="card-body">
                @if (! $event->activity)
                    <p class="card-text text-center">
                        No sign-up details are currently configured.
                    </p>

                    <hr />
                @endif

                <div class="row">
                    <div class="col-md-6">
                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'registration_start',
                                'label' => 'Sign-up start:',
                                'placeholder' => old('registration_start')
                                    ? strtotime(old('registration_start'))
                                    : ($event->activity
                                        ? $event->activity->registration_start
                                        : null),
                            ]
                        )
                    </div>

                    <div class="col-md-6">
                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'registration_end',
                                'label' => 'Sign-up end:',
                                'placeholder' => old('registration_end')
                                    ? strtotime(old('registration_end'))
                                    : ($event->activity
                                        ? $event->activity->registration_end
                                        : null),
                            ]
                        )
                    </div>

                    <div class="col-md-6">
                        @include(
                            'components.forms.datetimepicker',
                            [
                                'name' => 'deregistration_end',
                                'label' => 'Sign-out end:',
                                'placeholder' => old('registration_end')
                                    ? strtotime(old('deregistration_end'))
                                    : ($event->activity
                                        ? $event->activity->deregistration_end
                                        : null),
                            ]
                        )
                    </div>

                    <div class="col-md-6">
                        <label for="price">Participation cost:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text"
                                    id="basic-addon1"
                                >
                                    &euro;
                                </span>
                            </div>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                class="form-control"
                                id="price"
                                name="price"
                                value="{{ $event->activity ? $event->activity->price : '0' }}"
                                placeholder="15"
                                required
                            />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="no_show_fee">
                            <i
                                class="fas fa-question-circle me-2"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-html="true"
                                title="Input only the additional no show fee."
                            ></i>
                            No show fee:
                        </label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text"
                                    id="basic-addon1"
                                >
                                    &euro;
                                </span>
                            </div>
                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                class="form-control"
                                id="no_show_fee"
                                name="no_show_fee"
                                value="{{ old('no_show_fee') ?? ($event->activity ? $event->activity->no_show_fee : '0') }}"
                                placeholder="15"
                                required
                            />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="participants">
                            <i
                                class="fas fa-question-circle me-2"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-html="true"
                                title="Use -1 for unlimited.<br>Use 0 for helpers only."
                            ></i>
                            Participant limit:
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="participants"
                            name="participants"
                            min="-1"
                            required
                            value="{{ old('participants', $event->activity?->participants) }}"
                        />
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 mb-3">
                            <label for="redirect_url">Redirect URL</label>
                            <i
                                class="fas fa-question-circle me-2"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-html="true"
                                title="The URL you get redirect to after signing up!."
                            ></i>
                            <input
                                type="url"
                                class="form-control"
                                id="redirect_url"
                                name="redirect_url"
                                placeholder="https://forms.gle/..."
                                value="{{ $event->activity?->redirect_url ?? '' }}"
                            />
                        </div>
                    </div>

                    <div class="col-md-6">
                        @include(
                            'components.forms.checkbox',
                            [
                                'name' => 'hide_participants',
                                'checked' =>
                                    (isset($request) && $request->exists('hide_participants')) ||
                                    $event->activity?->hide_participants,
                                'label' =>
                                    'Hide participants  <i class="fas fa-question-circle me-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="This will hide who participates in this event for members!"></i>',
                            ]
                        )
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="row justify-content-center">
                    <div class="col-6">
                        <input
                            type="submit"
                            class="btn btn-success btn-block"
                            value="Save sign-up details"
                        />
                    </div>

                    @if ($event->activity)
                        <div class="col-6">
                            <a
                                href="{{ route('event::deletesignup', ['id' => $event->id]) }}"
                                class="btn btn-danger btn-block"
                            >
                                Remove sign-up
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    @else
        <div class="card-body">
            <p class="card-text text-center">
                You must save this event before being able to add sign-up
                details.
            </p>
        </div>
    @endif
</div>
