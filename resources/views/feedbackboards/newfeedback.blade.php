<form class="form-horizontal" method="post" action="{{ route("feedback::add", ['category'=>$category]) }}">

    {{ csrf_field() }}

    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            Add your {{ strtolower($category->title) }}
        </div>

        <div class="card-body">
            <textarea class="form-control" rows="4" cols="30" name="idea" required
                      placeholder="An online {{strtolower($category->title)}} board."></textarea>
        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-success btn-block">
                Submit
            </button>

        </div>

    </div>

</form>