@if((Carbon::now()->month===Carbon::DECEMBER) && Cookie::get('disable-december')!=='disabled')
    @vite('resources/assets/sass/december.scss')
    @for($count=0; $count<12*12; $count++)
        <div class="snowflake"></div>
    @endfor
@elseif(Auth::check() && Auth::user()->theme)
    @vite("resources/assets/sass/".Config::array('proto.themes')[Auth::user()->theme].".scss")
@else
    @vite('resources/assets/sass/light.scss')
@endif

{{--load the font used on our website and prefetch it--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
    rel="stylesheet">
