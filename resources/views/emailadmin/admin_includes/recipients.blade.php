@if ($email)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">Recipients</div>

        <div class="card-body">
            <p class="card-text">
                <strong>
                    {{ $email->recipients()->count() }} people will receive
                    this e-mail:
                </strong>
            </p>

            <p
                class="card-text"
                style="max-height: 200px; overflow-y: auto; overflow-x: hidden"
            >
                @foreach ($email->recipients() as $recipient)
                    {{ $recipient->name }}
                    <br />
                @endforeach
            </p>
        </div>
    </div>
@endif
