@if ($errors->any())
    <div class="card mb-3 bg-danger">
        <div class="card-body">
            <ul class="list-group p-3">
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
