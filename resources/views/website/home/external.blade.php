@extends('website.home.shared')

@section('greeting')

    <strong>Welcome to S.A. Proto</strong><br>
    Study Association of Creative Technology and Interaction Technology

@endsection

@section('left-column')

    <div class="row row-eq-height">

        <div class="col-md-12 col-xl-6 mb-3">

            <div class="card leftborder leftborder-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Projects</h5>
                    <p class="card-text">
                        Throughout the whole curriculum, Creative Technology students are faced with big ideas and
                        little time. Through experience, the students learn to what extent they can develop their
                        prototypes. From quick electrical circuits just to show that it works to advanced prototypes
                        that only need better building materials: CreaTe students have seen it all.

                    </p>
                    <p class="card-text">
                        Interaction Technology aims to combine a scientific mindset with specialist technical knowledge.
                        I-Techers analyse, design, validate and implement intelligent interactive systems in their
                        operational context. Recent developments in hardware and computational technologies are used in
                        all kinds of projects. Students are taught to become the new engineer: bridging disciplines
                        within project groups and understanding tech and their users.
                    </p>
                </div>
            </div>

        </div>

        <div class="col-md-12 col-xl-6 mb-3">

            <div class="card leftborder leftborder-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Community</h5>
                    <p class="card-text">
                        Active and eager students from Creative Technology and Interaction Technology come together in
                        Study Association Proto. One thing Proto stands out for is its diverse, but close community.
                        Students with different nationalities, experiences, interests and skills form the basis on which
                        this community is built. Currently, Proto consists of circa 700 members and 35 committees, all
                        held together by a full-time student board.
                    </p>
                    <p class="card-text">
                        Besides organising lots of interesting activities for entertainment and/or educational purposes,
                        the community also strengthens itself with its various ways of thinking: whether it’s more
                        practical and technical, focused on design or art, or even business: everyone can deliver his or
                        her input.
                    </p>
                </div>
            </div>

        </div>

    </div>

    <div class="row row-eq-height">

        <div class="col-md-12 col-xl-6 mb-3">

            <div class="card leftborder leftborder-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Creative</h5>
                    <p class="card-text">
                        Where time is always the issue, creativity most certainly is not. With knowledge of user group
                        habits, the latest sensors, interfaces, databases and their advantages and limits, Creative
                        Technology and Interaction Technology students learn how to connect the right dots. Thanks to
                        many brainstorm lessons and sessions, the students know how to step outside the box and let
                        their ideas flow, whether it is on their own or within a (multidisciplinary) project group.
                    </p>
                </div>
            </div>

        </div>

        <div class="col-md-12 col-xl-6 mb-3">

            <div class="card leftborder leftborder-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Innovation</h5>
                    <p class="card-text">
                        Combine creativity with technical knowledge and the possibilities are endless. Now include
                        entrepreneurial mindsets and an eye for missing products and new innovations arise. Innovation
                        and improvement of our daily life is the key feature to a Creative or Interaction Technologist’s
                        philosophy.
                    </p>
                </div>
            </div>

        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-12 col-xl-6">
            <a href="{{ route('page::show', ['slug' => 'contact']) }}" class="btn btn-info btn-block mb-3">
                <i class="fas fa-user me-2" aria-hidden="true"></i> Contact
            </a>
        </div>

        <div class="col-md-12 col-xl-6">
            <a href="{{ route("event::index") }}" class="btn btn-info btn-block">
                <i class="far fa-calendar-alt me-2" aria-hidden="true"></i> Upcoming events
            </a>
        </div>

    </div>

@endsection

@section('right-column')

    @include('website.home.cards.recentalbums', ['albums'=>$albums])

    @if(isset($videos) && count($videos) > 0)

        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fab fa-youtube fa-fw me-2"></i> Recent videos
            </div>
            <div class="card-body">

                @foreach($videos as $video)

                    @include('videos.includes.video_block', [
                        'video' => $video,
                        'photo_pop' => false
                    ])

                @endforeach

            </div>
        </div>

    @endif

@endsection
