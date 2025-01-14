<div class="card mb-3">
    <div class="card-header">Codex details</div>
    <div class="card-body">
        <!-- Name -->
        <div class="mb-3">
            <label for="name">Name:</label>
            <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                placeholder="A super cool codex!"
                value="{{ $codex->name ?? '' }}"
                required
            />
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="export">Document name:</label>
            <input
                type="text"
                class="form-control"
                id="export"
                name="export"
                value="{{ $codex->export ?? '' }}"
            />
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description">Description:</label>
            <input
                type="text"
                class="form-control"
                id="description"
                name="description"
                value="{{ $codex->description ?? '' }}"
            />
        </div>
    </div>
</div>
