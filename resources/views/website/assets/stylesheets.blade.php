@if(Auth::check() && Auth::user()->theme)
    @vite("resources/sass/".config('proto.themes')[Auth::user()->theme].".scss")
@else
    @vite('resources/sass/light.scss')
@endif