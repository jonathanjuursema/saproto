<footer class="main-footer bg-dark text-white py-3 mt-3">

    <div class="container">

        <div class="row vcard">

            <div class="col-md-3 col-sm-6 col-5">
                <strong>
                    <span class="fas fa-home"></span>&nbsp;&nbsp;
                    <a class="org url text-white" href="https://www.saproto.nl/"><span
                            class="green">S.A. Proto</span></a>
                </strong>
                <br>
                <span class="adr">
                    <span class="extended-address">Zilverling A230</span><br>
                    <span class="street-address">Drienerlolaan 5</span><br>
                    <span class="postal-code">7522NB</span>
                    <span class="locality">Enschede</span><br>
                </span>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 col-7">
                <br>
                <span class="fas fa-clock fa-fw"></span>&nbsp;&nbsp;Monday & Friday 08:30-16:00<br>
                <span class="fas fa-clock fa-fw"></span>&nbsp;&nbsp;Tuesday-Thursday, 08:30-17:30<br>
                <span class="fas fa-phone fa-fw"></span>&nbsp;&nbsp;<a class="tel text-white" href="tel:+31534894423">
                    +31 (0)53 489 4423
                </a><br>
                <span class="fas fa-envelope fa-fw"></span>&nbsp;&nbsp;
                <a class="email text-white"
                   href="mailto:board@proto.utwente.nl">board@proto.utwente.nl</a>
            </div>

            <div class="col-3 d-none d-lg-block text-end">
                <br>
                <a class="text-white" href="https://www.youtube.com/channel/UCdH2x3ObOrmLdm2OOGDBslw" target="_blank">
                    YouTube&nbsp;&nbsp;<i class="fab fa-fw fa-youtube" aria-hidden="true"></i></a><br>
                <a class="text-white" href="https://www.linkedin.com/company/s-a-proto/" target="_blank">
                    LinkedIn&nbsp;&nbsp;<i class="fab fa-fw fa-linkedin" aria-hidden="true"></i></a><br>
                <a class="text-white" href="https://www.instagram.com/s.a.proto/" target="_blank">
                    Instagram&nbsp;&nbsp;<i class="fab fa-fw fa-instagram" aria-hidden="true"></i></a><br>
                <a class="text-white" href="https://www.proto.utwente.nl/page/privacy" target="_blank">
                    Privacy Policy&nbsp;&nbsp;<i class="fa fa-shield" aria-hidden="true"></i></a><br>
            </div>

            <div class="col-lg-3 col-md-5 d-none d-md-block text-end">
                <img height="120px" width="472px" src="{{ asset('images/logo/inverse.png') }}"
                     alt="inverse proto logo">
            </div>

        </div>

        <p class="text-center mt-3 mb-2">
            <sub>
                &copy; {{ date('Y') }} S.A. Proto. All rights reserved.
                <span class="d-sm-none">
                    Please familiarize yourself with our
                    <a href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf" target="_blank"
                       class="text-white">
                        privacy policy
                    </a> and <a href="https://wiki.proto.utwente.nl/ict/responsible-disclosure" class="text-white">
                        responsible disclosure policy
                    </a>.
                    </span>
                The website source is available on
                <a href="https://github.com/saproto/saproto" target="_blank" class="text-white">
                    GitHub
                </a>.
                <br>
                This website has been created with <i class="fas fa-heart text-primary"></i> by the folks of the
                <a href="{{ 'https://'.config('proto.domains.developers')[0] }}" target="_blank" class="text-white">
                    Have You Tried Turning It Off And On Again committee
                </a>.
            </sub>
        </p>

    </div>
</footer>
