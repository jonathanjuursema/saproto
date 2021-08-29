<div class="wrapper vh-100 bg-dark col-2 p-0" style="min-width: 250px">
    <nav id="category-nav" class="nav p-3">
        @foreach($categories as $category)
            <div class="btn btn-lg btn-category btn-block bg-omnomcom rounded-0 px-4 py-2 text-left {{ ($category == $categories[0] ? '' : 'inactive') }}"
                 style="max-height: 50px" data-id="{{ $category->category->id }}">
                {{ $category->category->name }}
            </div>
        @endforeach

        @if(count($minors) > 0)
            <div class="btn btn-lg btn-category btn-block bg-omnomcom rounded-0 px-4 py-2 text-left inactive" data-id="static-minors">
                <strong>{{ count($minors) }}</strong> Minor Members
            </div>
        @endif

        @if(Auth::check())
            <a href="{{ route('login::logout::redirect', ['route' => 'omnomcom::store::show']) }}" class="btn btn-lg btn-block bg-omnomcom rounded-0 px-4 py-2 mt-4 text-left ellipsis inactive" onclick="window.location='{{ route("login::logout") }}'">
                Log out <strong>{{ Auth::user()->calling_name }}</strong>
            </a>
        @endif

        <div id="reload-button" class="btn btn-category btn-block px-4 py-2">
            RELOAD BUTTON
        </div>
    </nav>
</div>