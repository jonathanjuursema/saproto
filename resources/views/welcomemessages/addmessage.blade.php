<form class="form-horizontal" method="post" action="{{ route("welcomeMessages.store") }}" id="addmessage">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-4">
            <div id="user-select" class="form-group autocomplete">
                <input class="form-control user-search" name="user_id" required />
            </div>
        </div>
        <div class="col-md-6">
            <input id="message" class="form-control" name="message" placeholder="I'm watching you..." type="text"
                   required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success btn-block">
                Post
            </button>
        </div>
    </div>
</form>
