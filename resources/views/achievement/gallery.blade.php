@extends('website.layouts.redesign.generic')

@section('page-title')
    Achievement Overview
@endsection

@section('container')

    <div id="achievement-accordion">

        @foreach(['common' => $common, 'uncommon' => $uncommon, 'rare' => $rare, 'epic' => $epic, 'legendary' => $legendary] as $tier => $achievements)

            <div class="card mb-3 achievement-{{ $tier }}" id="achievement-{{ $tier }}">

                <div class="card-header text-reset cursor-pointer" data-bs-toggle="collapse"
                     data-bs-target="#collapse-achievement-{{ $tier }}">

                    @for($i = 0; $i < 5; $i++)
                        @if($i< $achievements[0]->numberOfStars())
                            <i class="text-white fas fa-star"></i>
                        @else
                            <i class="achievement-{{ $tier }} fas fa-star"></i>
                        @endif
                    @endfor

                    <span class="text-capitalize ms-3">
                    <strong class="text-white">{{ $tier }}</strong>
                </span>

                </div>

                <div class="card-body collapse {{ ($tier == 'common' ? 'show' : '') }}"
                     id="collapse-achievement-{{ $tier }}" data-parent="#achievement-accordion">

                    <div class="row">

                        @if(count($achievements) > 0)

                            @foreach($achievements as $achievement)

                                <div class="col-xl-4 col-md-6 col-sm-12">

                                    @include('achievement.includes.achievement_include', [
                                    'achievement' => $achievement,
                                    'obtained'=>$obtained?->filter(function($item) use ($achievement) { return $item->id == $achievement->id; })->first()?->pivot
                                    ])

                                </div>

                            @endforeach

                        @endif

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@endsection
