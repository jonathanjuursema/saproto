@extends('website.layouts.redesign.generic')

@section('page-title')
    Achievement Overview
@endsection

@section('container')

    <div id="achievement-accordion">

        <?php $stars = 1; ?>

        @foreach(['common' => $common, 'uncommon' => $uncommon, 'rare' => $rare, 'epic' => $epic, 'legendary' => $legendary] as $tier => $achievements)

            <div class="card mb-3 achievement-{{ $tier }}" id="achievement-{{ $tier }}">

                <div class="card-header text-white cursor-pointer" data-bs-toggle="collapse"
                     data-bs-target="#collapse-achievement-{{ $tier }}">

                    @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star"></i>
                    @endfor

                    <span class="text-capitalize ms-3">
                    <strong>{{ $tier }}</strong>
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
                                    'obtained'=>$obtained->filter(function($item) use ($achievement) { return $item->id == $achievement->id; })->first()?->pivot
                                    ])

                                </div>

                            @endforeach

                        @endif

                        <?php $stars++; ?>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@endsection