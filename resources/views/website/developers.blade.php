@extends('website.layouts.default-nobg')

@section('page-title')
    About the website of S.A. Proto
@endsection

@section('content')

    <div class="row">

        <div class="col-md-7">

            <div class="panel panel-default container-panel">

                <div class="panel-body">

                    <h3>About this website</h3>

                    <p>
                        You are currently looking a the website of Study Association Proto, released in the summer of
                        2016. For as far as you're interested, it is mostly custom-built on the Laravel framework. It is
                        the successor of the horrible WordPress website we've been rocking since the association was
                        founded in 2011.
                    </p>

                    <p>
                        The website is open source and actively maintained by a dedicated committee of the association:
                        the <a href="{{ route('committee::show', ['id'=>$committee->id]) }}">{{ $committee->name }}</a>.
                        You will find the current members of this committee on your right. Below this piece of prose you
                        will also find a list of the developers who have been contributing to the association's ICT in
                        the past.
                    </p>

                    <h3>Responsible disclosure</h3>

                    <p>
                        If you find any security flaw on our website, please <a
                                href="mailto:{{ $committee->slug . "@" . config('proto.emaildomain') }}">e-mail the
                            developers</a> immediately. We will make sure the security hole gets fixed as soon as
                        possible.
                    </p>

                    <h3>Questions and feedback</h3>

                    <p>
                        If you have questions and/or feedback regarding this website, you are very most welcome to
                        submit it.
                    </p>

                    <p>
                        If you have a problem with the content of the website, please contact <a
                                href="mailto:board@proto.utwente.nl">the board of the association</a>. They generally
                        decide what gets published and are able to make general changes to user accounts, committees,
                        activities and other association-related content.
                    </p>

                    <p>
                        For issues related to the website itself, you can get into contact with the developers. We are
                        active on <a href="https://github.com/saproto/saproto" target="_blank">GitHub</a> where we
                        contribute code to the website and resolve issues. If you have any technical issue, bug report
                        or feature request, we encourage you to open an issue on GitHub. This way you'll be kept in the
                        loop on your particular thing.
                    </p>

                    <p>
                        If you feel the desire to contribute to the website directly, do not hestitate to fork our
                        repository and make a pull request with your changes. We welcome all input and be happy to help
                        you get your idea integrated in the website! Alternatively, you can always <a
                                href="mailto:{{ $committee->slug . "@" . config('proto.emaildomain') }}">send an e-mail
                            directly</a>, though for issues we will usually redirect you to GitHub anyway.
                    </p>

                </div>

            </div>

            <div class="panel panel-default container-panel">

                <div class="panel-body">

                    <h3>Developers from days past</h3>

                    @foreach($developers['old'] as $i => $dev)
                        <a href="{{ route('user::profile', ['id' => $dev->user->id]) }}">
                            {{ $dev->user->name }}
                        </a>
                        @if ($i + 1 < count($developers['old']))
                            ,
                        @endif
                    @endforeach

                </div>

            </div>

        </div>

        <div id="developer__list" class="col-md-5">

            @if($committee->image)
                <img src="{{ $committee->image->generateImagePath(800,300) }}"
                     style="width: 100%; margin-bottom: 30px; box-shadow: 0 0 20px -7px #000;">
            @endif

            @foreach($developers['current'] as $i => $dev)

                @if($i % 2 == 0)

                    <div class="col">

                        @endif

                        <div class="col-md-6">

                            <div class="developer__list__entry"
                                 style="background-image: url('{!! ($dev->user->photo ? $dev->user->photo->generateImagePath(250, 250) : '') !!}');">

                                <span>
                                    <a href="{{ route('user::profile', ['id' => $dev->user->id]) }}">
                                        {{ $dev->user->name }}
                                    </a>
                                </span>

                            </div>

                        </div>

                        @if($i % 2 == 1 || $i == count($developers['current']) - 1)

                    </div>

                @endif

            @endforeach

        </div>

    </div>

@endsection

@section('stylesheet')

    @parent

    <style type="text/css">

        .developer__list__entry {
            position: relative;

            width: 100%;
            height: 200px;
            margin-bottom: 15px;

            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;

            background-color: #222;
            box-shadow: 0 0 20px -7px #000;
        }

        .developer__list__entry > span {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;

            height: 30px;
            line-height: 30px;

            padding: 0 10px;

            background-color: rgba(0, 0, 0, 0.5);
        }

        .developer__list__entry > span > a {
            color: #fff !important;
        }

    </style>

@endsection