<script type="text/javascript" src="{{ asset('assets/application.min.js') }}"></script>

<script type="text/javascript">
    moment.updateLocale('en', {
        week: {dow: 1}
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {

        // Enables tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

        // Enables the fancy scrolling effect
        $(window).scroll(function () {
            var scroll = $(window).scrollTop();

            if (scroll >= 100) {
                $("#nav").addClass("navbar-scroll");
            } else {
                $("#nav").removeClass("navbar-scroll");
            }
        });

        @if (Auth::check() && Auth::user()->member && Cookie::get('hideSlack', 'show') === 'show')
        initSlack('{{ route('api::slack::count') }}', '{{ route('api::slack::invite') }}');
        @endif


    });

    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-36196842-2', 'auto');
    ga('send', 'pageview');

</script>

<script type="text/javascript">
    function initSlack(countRoute, inviteRoute) {

        $.ajax({
            'url': countRoute,
            'success': function (data) {
                $("#slack__online").html(data);
            },
            'error': function () {
                $("#slack__online").html('0');
            }
        });

        $("#slack__invite").on('click', function () {
            $("#slack__invite").html("Working...");
            $.ajax({
                'url': inviteRoute,
                'success': function (data) {
                    $("#slack__invite").html(data).attr("disabled", true);
                },
                'error': function () {
                    $("#slack__invite").html('Something went wrong...');
                }
            });
        });

        $("#slack__hide").on('click', function () {
            if (confirm("This will hide the Slack status button from your navigation bar. The only way to undo this action is to clear your browser cookies. Are you sure?")) {
                document.cookie = "hideSlack=yesSir; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
                window.location.reload();
            }
        });

    }
</script>

<!-- Matomo -->
<script type="text/javascript">
    var _paq = _paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function () {
        var u = "//metis.proto.utwente.nl/analytics/";
        _paq.push(['setTrackerUrl', u + 'piwik.php']);
        _paq.push(['setSiteId', '1']);
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = u + 'piwik.js';
        s.parentNode.insertBefore(g, s);
    })();
</script>
<!-- End Matomo Code -->

@if(Auth::user() && Auth::user()->can('admin'))
    <script type="text/javascript">

        $.fn.select2.defaults.set("theme", "default");

        $(".user-search").select2({
            ajax: {
                url: "{{ route('api::search::user') }}",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: false
            },
            placeholder: 'Start typing a name...',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                } else if (item.is_member) {
                    opacity = 1;
                } else {
                    opacity = 0.3;
                }
                return "<div class='member ellipsis'>" +
                    "<div class='member-picture' style='background-image:url(\"" + item.photo_preview + "\");'></div>" +
                    "<span style='opacity: " + opacity + "'>" + item.name + " (#" + item.id + ")</span>" +
                    "</div>";
            },
            templateSelection: function (item) {
                if (item.id == "") {
                    return item.text;
                } else {
                    return item.name + " (#" + item.id + ")";
                }
            }
        });

        $(".event-search").select2({
            ajax: {
                url: "{{ route('api::search::event') }}",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: false
            },
            placeholder: 'Start typing...',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                } else if (item.is_future) {
                    opacity = 1;
                } else {
                    opacity = 0.3;
                }
                return "<span style='opacity: " + opacity + "'>" + item.title + " (" + item.formatted_date.simple + ")</span>";
            },
            templateSelection: function (item) {
                if (item.id == "") {
                    return item.text;
                } else {
                    return item.title + " (" + item.formatted_date.simple + ")";
                }
            },
            sorter: function (data) {
                return data.sort(function (a, b) {
                    if (a.start < b.start) {
                        return 1;
                    } else if (a.start > b.start) {
                        return -1;
                    } else {
                        return 0;
                    }
                });
            }
        });

        $(".product-search").select2({
            ajax: {
                url: "{{ route('api::search::product') }}",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: false
            },
            placeholder: 'Start typing a name...',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                } else if (item.is_visible) {
                    opacity = 1;
                } else {
                    opacity = 0.3;
                }
                return "<span style='opacity: " + opacity + "'>" + item.name + " (€" + item.price.toFixed(2) + "; " + item.stock + " in stock)</div>";
            },
            templateSelection: function (item) {
                if (item.id == "") {
                    return item.text;
                } else {
                    return item.name;
                }
            },
            sorter: function (data) {
                return data.sort(function (a, b) {
                    if (a.is_visible == 0 && b.is_visible == 1) {
                        return 1;
                    } else if (a.is_visible == 1 && b.is_visible == 0) {
                        return -1;
                    } else {
                        return 0;
                    }
                });
            }
        });

        $(".committee-search").select2({
            ajax: {
                url: "{{ route('api::search::committee') }}",
                dataType: 'json',
                delay: 50,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: false
            },
            placeholder: 'Start typing a name...',
            minimumInputLength: 1,
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                return item.name;
            },
            templateSelection: function (item) {
                if (item.id == "") {
                    return item.text;
                } else {
                    return item.name;
                }
            }
        });

    </script>
@endif