<form
    method="post"
    enctype="multipart/form-data"
    action="{{ route('user::pic::update') }}"
>
    @csrf

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">Profile photo</div>

        <div class="card-body">
            <div class="row">
                <div class="col-5 d-flex align-items-center">
                    <h5 class="text-center">
                        This is you
                        <i class="far fa-hand-point-right ms-2"></i>
                    </h5>
                </div>

                <div class="col-7 text-center">
                    <img
                        src="{{ $user->generatePhotoPath(150, 150) }}"
                        width="150px"
                        height="150px"
                        class="rounded-circle"
                    />
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-9">
                    <div class="input-group">
                        <input
                            id="profile-pic"
                            name="image"
                            type="file"
                            class="form-control"
                        />
                        <button type="submit" class="btn btn-outline-info">
                            <i class="fas fa-file-upload"></i>
                        </button>
                    </div>
                </div>
                <div class="col-3">
                    <a
                        href="{{ route('user::pic::delete') }}"
                        class="btn btn-outline-danger btn-block"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
