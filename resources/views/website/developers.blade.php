@extends('website.layouts.redesign.generic-nonav')

@section('page-title')
    About the website of S.A. Proto
@endsection

@section('container')

    <div class="row">

        <div class="col-md-7">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <a class="btn btn-warning btn-block"
                       href="https://wiki.proto.utwente.nl/ict/services">
                        <i class="fas fa-info-circle me-2"></i>
                        Manuals
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a class="btn btn-info btn-block"
                       href="https://wiki.proto.utwente.nl/ict/issues">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Learn how to report errors!
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a class="btn btn-success btn-block"
                       href="https://wiki.proto.utwente.nl/ict/frequent-problems">
                        <i class="fas fa-question-circle me-2"></i>
                        Answers to frequent questions
                    </a>
                </div>
            </div>

            <div class="card mb-3">

                <div class="card-header bg-dark text-white">
                    About this website
                </div>

                <div class="card-body">

                    <p class="card-text">
                        The <a href="{{ route("homepage") }}">website of Study Association Proto</a> was released in the
                        summer of 2016. For as far as you're interested, it is mostly custom-built on the Laravel
                        framework. It is the successor of the WordPress website we've been rocking since the association
                        was founded in 2011.
                    </p>

                    <p class="card-text">
                        The website is open source and actively maintained by a dedicated committee of the association:
                        the
                        <a href="{{ route('committee::show', ['id'=>$committee->getPublicId()]) }}">{{ $committee->name }}</a>.
                        You will find the current members of this committee on your right. Below this piece of prose you
                        will also find a list of the developers who have been contributing to the association's ICT in
                        the past.
                    </p>

                    <h5 class="card-title">Responsible disclosure</h5>

                    <p class="card-text">
                        If you find any security flaw on our website, please <a
                            href="mailto:{{ $committee->slug . "@" . Config::string('proto.emaildomain') }}">e-mail
                            the
                            developers</a> immediately. We will make sure the security hole gets fixed as soon as
                        possible.
                    </p>

                    <h5 class="card-title">Reporting issues and giving feedback</h5>

                    <p class="card-text">
                        If you have a problem with the content of the website, please contact <a
                            href="mailto:board@proto.utwente.nl">the board of the association</a>. They generally
                        decide what gets published and are able to make general changes to user accounts, committees,
                        activities and other association-related content.
                    </p>

                    <p class="card-text">
                        For issues related to the website itself, you can get into contact with the developers. We are
                        active on <a href="https://github.com/saproto/saproto" target="_blank">GitHub</a> where we
                        contribute code to the website and resolve issues. If you have any technical issue, bug report
                        or feature request, we encourage you to open an issue on GitHub. This way you'll be kept in the
                        loop on your particular thing.
                    </p>

                    <p class="card-text">
                        If you have questions and/or feedback regarding this website, you are very most welcome to
                        submit them.
                    </p>

                    <p class="card-text">
                        If you feel the desire to contribute to the website directly, do not hestitate to fork our
                        repository and make a pull request with your changes. We welcome all input and be happy to help
                        you get your idea integrated in the website! Just want to ask something? <a
                            href="mailto:{{ $committee->slug . "@" . Config::string('proto.emaildomain') }}">Shoot
                            us an
                            e-mail!</a>
                    </p>

                </div>

                <div class="card-footer text-center">
                    <strong>Previous contributors</strong> -
                    @foreach($developers['old'] as $i => $dev)
                        @if($dev->user->isMember)
                            <a
                                href="{{ route('user::profile', ['id' => $dev->user->getPublicId()]) }}">
                                {{ $dev->user->name }}
                            </a>
                        @else
                            {{ $dev->user->name }}
                        @endif
                        @if ($i + 1 < count($developers['old']))
                            -
                        @endif
                    @endforeach
                </div>

            </div>

        </div>

        <div class="col-md-5">

            <div class="card mb-3">

                @if($committee->image)
                    <img class="card-img-top w-100" src="{{ $committee->image->generateImagePath(800,300) }}">
                @endif

                <div class="card-body">

                    <div class="row">

                        @foreach($developers['current'] as $i => $dev)

                            <div class="col-6">

                                @include('users.includes.usercard', [
                                    'user' => $dev->user,
                                    'subtitle' => sprintf('<em>%s</em>', $dev->role)
                                ])

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
