<div class="card mb-3">

    <div class="card-header bg-dark text-white">
        Password
    </div>

    <div class="card-body">

        <a href="{{ route('login::password::change::index') }}" class="btn btn-outline-info btn-block">
            Change Proto password
        </a>

        <a href="https://tap.utwente.nl/protected/chpw1.php" target="_blank"
           class="btn btn-outline-secondary btn-block">
            Change UTwente password
        </a>

        <hr>

        <p class="card-text">
            This may resolve issues you have logging in to external Proto services such as e-mail, network drives or the
            wiki.
        </p>

        <a href="{{ route('login::password::sync::index') }}" class="btn btn-outline-info btn-block">
            Synchronize passwords
        </a>

    </div>

</div>
