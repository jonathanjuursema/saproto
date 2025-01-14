<div class="card mb-3">
    <div class="card-header bg-dark text-white">OmNomCom cards</div>

    <div class="card-body">
        @if (count($user->rfid) > 0)
            <div class="row">
                @foreach ($user->rfid as $rfid)
                    <div class="col-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="card-text">
                                    {{ $rfid->name ?? 'Nameless card' }}
                                    <br />
                                    <sup class="texttext-muted">
                                        Last used: {{ $rfid->updated_at }}
                                    </sup>
                                    <br />

                                    <a
                                        class="btn btn-sm btn-outline-danger"
                                        href="{{ route('user::rfid::delete', ['id' => $rfid->id]) }}"
                                    >
                                        Delete
                                    </a>

                                    <a
                                        class="btn btn-sm btn-outline-info"
                                        href="{{ route('user::rfid::edit', ['id' => $rfid->id]) }}"
                                    >
                                        Rename
                                    </a>

                                    <br />

                                    <sub class="text-muted">
                                        Card ID: {{ $rfid->card_id }}
                                    </sub>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="card-text text-center">
                You have no cards linked to your account. You can link cards at
                the OmNomCom.
            </p>
        @endif
    </div>
</div>
