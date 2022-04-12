@if(Auth::check() && Auth::user()->theme)
    <link rel="stylesheet" href="{{ mix("/css/application-".config('proto.themes')[Auth::user()->theme].".css") }}">
@else
    <link rel="stylesheet" href="{{ mix('/css/application-light.css') }}">
@endif