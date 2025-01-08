@if($user && $user->is_member)
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Custom OmNomCom Sound for {{$user->calling_name}}
        </div>
        @if($user->member->customOmnomcomSound)
            <div class="card-body">
                <div class="d-inline-flex flex-wrap justify-content-around mb-2 w-100">
                    <div class="d-flex align-items-center">
                        {{$user->calling_name}}'s custom sound:
                    </div>
                    <audio controls class="mw-100">
                        <source src="{{ $user->member->customOmnomcomSound->generatePath() }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                <a href="{{ route("user::member::omnomcomsound::delete", ['id'=>$user->id]) }}"
                   class="btn btn-outline-danger btn-block">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        @else
            <div class="text-center m-2">
                {{$user->calling_name}} does not have a custom omnomcom sound! Upload an <b>mp3</b> below.<br>
                <i>Note: keep it under 200kb and 5 seconds!</i>
            </div>
        @endif

        <form method="post" action="{{ route('user::member::omnomcomsound::update', ['id'=>$user->id]) }}"
              enctype="multipart/form-data">
            @csrf
            <div class="card-footer">
                <div class="row">
                    <div class="input-group flex-nowrap">
                        <div class="custom-file">
                            <input id="sound" type="file" class="form-control" name="sound">
                        </div>
                        <button type="submit" class="btn btn-outline-info">
                            <i class="fas fa-file-upload"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endif
