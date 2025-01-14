@if ($event?->activity)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">Helping committees</div>

        <div class="card-body">
            <form
                method="post"
                action="{{ route('event::addhelp', ['id' => $event->id]) }}"
            >
                @csrf

                <div class="form-group autocomplete">
                    <input
                        class="form-control committee-search"
                        name="committee"
                        required
                    />
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input
                                type="number"
                                class="form-control"
                                name="amount"
                                placeholder="15"
                                min="1"
                                required
                            />
                            <span class="input-group-text" id="basic-addon2">
                                people
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <input
                            type="submit"
                            class="btn btn-success float-end"
                            value="Add"
                        />
                    </div>
                </div>
            </form>

            @if ($event->activity->helpingCommittees()->count() > 0)
                <hr />

                @foreach ($event->activity->helpingCommittees as $committee)
                    <p>
                        <strong>{{ $committee->name }}</strong>
                        <br />
                        Helps with
                        {{ $event->activity->helpingUsers($committee->pivot->id)->count() }}
                        people. {{ $committee->pivot->amount }} are needed.
                    </p>

                    <form
                        method="post"
                        action="{{ route('event::updatehelp', ['id' => $committee->pivot->id]) }}"
                    >
                        @csrf

                        <div class="row">
                            <div class="col-md-3">
                                <a
                                    href="{{ route('event::deletehelp', ['id' => $committee->pivot->id]) }}"
                                    class="btn btn-danger btn-sm btn-block"
                                >
                                    Delete
                                </a>
                            </div>

                            <div class="col-md-9">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                        >
                                            Update to
                                        </button>
                                    </div>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name="amount"
                                        min="1"
                                        required
                                        value="{{ $committee->pivot->amount }}"
                                    />
                                    <span
                                        class="input-group-text"
                                        id="basic-addon2"
                                    >
                                        people
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr />
                @endforeach
            @endif
        </div>
    </div>
@endif
