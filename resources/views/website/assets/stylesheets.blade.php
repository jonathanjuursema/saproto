@if((Carbon::now()->month===Carbon::DECEMBER) && Cookie::get('disable-december')!=='disabled')
    @vite('resources/assets/sass/december.scss')
    @for($count=0; $count<12*12; $count++)
        <div class="snowflake"></div>
    @endfor
@elseif(Auth::check() && Auth::user()->theme)
    @vite("resources/assets/sass/".config('proto.themes')[Auth::user()->theme].".scss")
@else
    @vite('resources/assets/sass/light.scss')
@endif