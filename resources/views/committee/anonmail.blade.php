@extends('website.layouts.redesign.generic')

@section('page-title')
    Send an anonymous e-mail to the {{ $committee->name }}
@endsection

@section('container')

    <div class="row justify-content-center">

        <div class="col-md-6 col-sm-8 col-xs-12">

            <form method="post"
                  action="{{ route("committee::sendanonymousmail", ["id" => $committee->getPublicId()]) }}">

                {!! csrf_field() !!}

                <div class="card">

                    <div class="card-header bg-dark text-white">
                        <strong>Compose your anonymous e-mail</strong>
                    </div>

                    <div class="card-body">

                        <p class="card-text">

                            Via this e-mail form you can send an anonymous e-mail to the {{ $committee->name }}. You can
                            use this form to contact the committee if you wish to share information with them without
                            revealing your identity.

                        </p>

                        <p class="card-text">

                            Please write your anonymous message below.

                        </p>

                        <textarea name="message" class="form-control" rows="10"></textarea>

                    </div>

                    <div class="card-footer">

                        <input type="submit" class="btn btn-success btn-block"
                               value="I have read and understand the privacy notice, please send my e-mail!">

                    </div>

                </div>

            </form>

        </div>

        <div class="col-md-4 col-sm-4 col-xs-12">

            <div class="card">

                <div class="card-header bg-danger text-white">
                    <strong>Important privacy notice!</strong>
                </div>

                <div class="card-body">

                    <p class="card-text">
                        Please take note that the receiving committee will not know your name and or e-mail address, so
                        they cannot get back to you. Also, in order to prevent spam, it is possible for the receiving
                        committee to notify the webmasters of the fact that spam has been sent. The webmasters and the
                        receiving committee <strong>together</strong> can infer who sent the e-mail, the receiving
                        committee alone can't infer anything and the webmasters can only infer who have been using the
                        system, but not what they sent. Please note that all webmasters have signed a <a
                            href="https://wiki.proto.utwente.nl/_media/ict/privacy/nda.pdf" target="_blank">non-disclosure
                            agreement</a>.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection
