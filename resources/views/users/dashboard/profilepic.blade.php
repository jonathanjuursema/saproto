<form method="post" enctype="multipart/form-data" action="{{ route("user::pic::update", ["id"=>$user->id]) }}" ng-app="app" ng-controller="Ctrl">

    {!! csrf_field() !!}

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Your profile photo</strong></div>
        <div class="panel-body">

            <!-- @if($user->photo)
                <div class="profile__photo-wrapper">
                    <img class="profile__photo" src="{{ $user->photo->generateImagePath(200, 200) }}" alt="">
                </div>
            @else
                <p style="text-align: center;">
                    You currently have no profile picture.
                </p>
            @endif -->

            <!-- <div>Select an image file: <input type="file" id="fileInput" /></div> -->
            <div class="cropArea">
              <img-crop image="myImage" area-type="square" result-image="myCroppedImage"></img-crop>
            </div>
            <hr>

            <input type="file" id="fileInput" name="image" class="form-control" required />

        </div>

        <div class="panel-footer">

            <div class="row">

                <div class="col-md-6">

                    <div class="btn-group btn-group-justified" role="group">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-success">
                                Update
                            </button>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="btn-group btn-group-justified" role="group">
                        <div class="btn-group" role="group">
                            <a href="{{ route("user::pic::delete", ["id"=>$user->id]) }}" class="btn btn-danger">
                                Delete
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</form>
