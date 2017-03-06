@extends('website.layouts.content')

@section('page-title')
    Homepage
@endsection

@section('header')

    <div id="header">

        <div class="container">

            @section('greeting')
            @show

        </div>

        <div class="greeting">Proto is <span id="greeting" ></span></div>

    </div>

@endsection

@section('container')

    @if(count($companies) > 0)

        <div class="row homepage__companyrow">

            <div class="homepage__companyrow__inner">

                @foreach($companies as $company)

                    <a href="{{ route('companies::show', ['id' => $company->id]) }}">
                        <img class="homepage__companyimage"
                             src="{{ $company->image->generateImagePath(null, 50) }}">
                    </a>

                @endforeach

            </div>

        </div>

    @endif

    <div class="container" style="margin-top: 30px;">

        @section('visitor-specific')
        @show

        <div class="row">

            <div class="col-md-4">

                <div class="panel panel-default homepage__calendar">

                    <div class="panel-body calendar">

                        <h4 style="text-align: center;">
                            Upcoming activities
                        </h4>

                        <hr>

                        <?php if (isset($events[0])) $week = date('W', $events[0]->start); ?>

                        @foreach($events as $key => $event)

                            @if($week != date('W', $event->start))
                                <hr>
                            @endif

                            <a class="activity"
                               href="{{ route('event::show', ['id' => $event->id]) }}">
                                <div class="activity {{ ($key % 2 == 1 ? 'odd' : '') }}" {!! ($event->secret ? 'style="opacity: 0.3;"' : '') !!}>
                                    <p><strong>{{ $event->title }}</strong></p>
                                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $event->location }}
                                    </p>
                                    <p>
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        {{ $event->generateTimespanText('l j F, H:i', 'H:i', '-') }}
                                    </p>
                                </div>
                            </a>

                            <?php $week = date('W', $event->start); ?>

                        @endforeach

                        <hr>

                        <a class="btn btn-success" style="width: 100%;" href="{{ route('event::list') }}">More upcoming events</a>

                    </div>

                </div>

            </div>

        </div>

        <div class="container">
            <div id="myCarousel" class="carousel slide" data-ride="carousel">

              <!-- Wrapper for slides -->
              <div class="carousel-inner">

                <div class="item active">
                  <img src="http://placehold.it/760x400/cccccc/ffffff">
                   <div class="carousel-caption">
                    <h4><a href="#">Lorem ipsum dolor sit amet consetetur sadipscing</a></h4>
                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                  </div>
                </div><!-- End Item -->

                 <div class="item">
                  <img src="http://placehold.it/760x400/999999/cccccc">
                   <div class="carousel-caption">
                    <h4><a href="#">consetetur sadipscing elitr, sed diam nonumy eirmod</a></h4>
                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                  </div>
                </div><!-- End Item -->

                <div class="item">
                  <img src="http://placehold.it/760x400/dddddd/333333">
                   <div class="carousel-caption">
                    <h4><a href="#">tempor invidunt ut labore et dolore</a></h4>
                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                  </div>
                </div><!-- End Item -->

                <div class="item">
                  <img src="http://placehold.it/760x400/999999/cccccc">
                   <div class="carousel-caption">
                    <h4><a href="#">magna aliquyam erat, sed diam voluptua</a></h4>
                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                  </div>
                </div><!-- End Item -->

                <div class="item">
                  <img src="http://placehold.it/760x400/dddddd/333333">
                   <div class="carousel-caption">
                    <h4><a href="#">tempor invidunt ut labore et dolore magna aliquyam erat</a></h4>
                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat. <a class="label label-primary" href="http://sevenx.de/demo/bootstrap-carousel/" target="_blank">Free Bootstrap Carousel Collection</a></p>
                  </div>
                </div><!-- End Item -->
              </div><!-- End Carousel Inner -->

            <ul class="list-group col-sm-6">
              <li data-target="#myCarousel" data-slide-to="0" class="list-group-item active"><h4>Lorem ipsum dolor sit amet consetetur sadipscing</h4></li>
              <li data-target="#myCarousel" data-slide-to="1" class="list-group-item"><h4>consetetur sadipscing elitr, sed diam nonumy eirmod</h4></li>
              <li data-target="#myCarousel" data-slide-to="2" class="list-group-item"><h4>tempor invidunt ut labore et dolore</h4></li>
              <!-- <li data-target="#myCarousel" data-slide-to="3" class="list-group-item"><h4>magna aliquyam erat, sed diam voluptua</h4></li>
              <li data-target="#myCarousel" data-slide-to="4" class="list-group-item"><h4>tempor invidunt ut labore et dolore magna aliquyam erat</h4></li> -->
            </ul>

              <!-- Controls -->
              <div class="carousel-controls">
                  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                  </a>
                  <a class="right carousel-control" href="#myCarousel" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                  </a>
              </div>

            </div><!-- End Carousel -->
        </div>

        <hr>

        <h1 style="text-align: center; color: #fff; margin: 30px;">
            Recent photo albums
        </h1>

        <div class="row">

            @foreach(Flickr::getAlbums(6) as $key => $album)

                <div class="col-md-4 col-xs-6">

                    <a href="{{ route('photo::album::list', ['id' => $album->id]) }}" class="album-link">
                        <div class="album"
                             style="background-image: url('{!! $album->thumb !!}')">
                            <div class="album-name">
                                {{ $album->name }}
                            </div>
                        </div>
                    </a>

                </div>

            @endforeach

        </div>

    </div>

@endsection

@section('javascript')

    @parent

    <script type="application/javascript">

        var sliderDelta = 0;
        var oneWayOrTheOther = true;

        $(window).load(function() {
            var boxheight = $('#myCarousel .carousel-inner').innerHeight();
            var itemlength = $('#myCarousel .item').length;
            var triggerheight = Math.round(boxheight/itemlength+1);
        	$('#myCarousel .list-group-item').outerHeight(triggerheight);
        });

        $(document).ready(function () {
            var clickEvent = false;
            $('#myCarousel').carousel({
                interval:   4000
            }).on('click', '.list-group li', function() {
                    clickEvent = true;
                    $('.list-group li').removeClass('active');
                    $(this).addClass('active');
            }).on('slid.bs.carousel', function(e) {
                if(!clickEvent) {
                    var count = $('.list-group').children().length -1;
                    var current = $('.list-group li.active');
                    current.removeClass('active').next().addClass('active');
                    var id = parseInt(current.data('slide-to'));
                    if(count == id) {
                        $('.list-group li').first().addClass('active');
                    }
                }
                clickEvent = false;
            });

            setTimeout(function () {

                updateSlider();

                setInterval(doSlide, 10000);

                doSlide();

            }, 2500);

            var theater = theaterJS()

            theater
            .on('type:start, erase:start', function () {
              // add a class to actor's dom element when he starts typing/erasing
              var actor = theater.getCurrentActor()
              actor.$element.classList.add('is-typing')
            })
            .on('type:end, erase:end', function () {
              // and then remove it when he's done
              var actor = theater.getCurrentActor()
              actor.$element.classList.remove('is-typing')
            })

            theater
                .addActor('greeting')

            theater
                .addScene('greeting:Lorem Ipsum...', 400)
                .addScene('greeting:Dolor sit amet?', 400)
                .addScene(theater.replay)

        });

        $(window).resize(updateSlider);

        function doSlide() {

            if (sliderDelta < 0) {
                if (oneWayOrTheOther) {
                    $(".homepage__companyrow__inner").css('left', sliderDelta + 'px');
                } else {
                    $(".homepage__companyrow__inner").css('left', '0px');
                }
            }

            oneWayOrTheOther = !oneWayOrTheOther;

        }

        setInterval(updateSlider, 1);

        function updateSlider() {
            sliderDelta = $(".homepage__companyrow").width() - $(".homepage__companyrow__inner").width() - 40;
        }

    </script>

@endsection
