@push('stylesheet')
    <style>
        #container {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9 !important;
            background-color: #333;
            margin: 0;
            padding: 0;
            padding-bottom: 56.25%;
            overflow: hidden;
        }

        #slideshow,
        #fullpagetext,
        #yt-player,
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 100%;
            height: 100%;

            background-color: #333;
        }

        #fullpagetext {
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 1s;
        }

        #text-container {
            text-align: center;
            font-size: 50px;
            color: #fff;
        }

        #slideshow,
        #yt-player {
            transition: all 1s;
        }

        .slide {
            background-size: cover;
            background-position: center center;
            transition: all 1s;
            opacity: 1;
        }
    </style>
@endpush

<div id="container">
    <div id="fullpagetext" class="opacity-0">
        <div id="text-container"></div>
    </div>

    <div id="slideshow" class="opacity-0"></div>

    <div id="yt-player" class="opacity-0 w-full"></div>
</div>

@push('javascript')
    <script
        type="text/javascript"
        src="https://www.youtube.com/iframe_api"
        nonce="{{ csp_nonce() }}"
    ></script>
    <script type="text/javascript" nonce="{{ csp_nonce() }}">
        let campaigns = []
        let currentCampaign = 0
        let previousWasVideo = false
        let youtubePlayer
        const hideClass = 'opacity-0'

        function onYouTubeIframeAPIReady() {
            youtubePlayer = new YT.Player('yt-player', {
                events: {
                    onReady: onPlayerReady,
                    onStateChange: onPlayerStateChange,
                },
                playerVars: {
                    modestbranding: 1,
                    controls: 0,
                    showinfo: 0,
                    disablekb: 1,
                    fs: 0,
                    iv_load_policy: 3,
                },
            })
            setTimeout(updateSlide, 1000)
        }

        async function updateCampaigns() {
            await get('{{ route('api::screen::narrowcasting') }}')
                .then((data) => {
                    if (
                        campaigns.length !== 0 &&
                        campaigns.length !== data.length
                    ) {
                        window.location.reload()
                    }

                    campaigns = data
                })
                .catch((error) =>
                    console.log('Error loading campaigns from server:', error)
                )
        }

        function onPlayerReady(event) {
            event.target.mute()
            event.target.playVideo()
            // updateSlide()
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.PLAYING) {
                setTimeout(
                    updateSlide,
                    (youtubePlayer.getDuration() - 1) * 1000
                )
            }
        }

        function updateSlide() {
            const text = document.getElementById('fullpagetext')
            const textContainer = document.getElementById('text-container')
            const slides = document.getElementById('slideshow')
            const player = document.getElementById('yt-player')

            if (campaigns.length === 0) {
                textContainer.innerHTML = 'There are no messages to display. :)'
                text.classList.remove(hideClass)
                slides.classList.add(hideClass)
                player.classList.add(hideClass)
                setTimeout(updateSlide, 1000)
            } else {
                textContainer.innerHTML = 'Loading slideshow... :)'
                text.classList.add(hideClass)
                player.classList.add(hideClass)
                slides.classList.add(hideClass)

                if (currentCampaign >= campaigns.length) {
                    currentCampaign = 0
                }
                const campaign = campaigns[currentCampaign]

                //hide the last slide
                let oldCampaign = currentCampaign - 1
                if (oldCampaign < 0) oldCampaign += campaigns.length
                const oldSlide = document.getElementById('slide-' + oldCampaign)
                if (oldSlide) {
                    oldSlide.classList.add(hideClass)
                }

                if (campaign.hasOwnProperty('image')) {
                    slides.classList.remove(hideClass)

                    //show the new slide if it exists, otherwise create it
                    const slide = document.getElementById(
                        'slide-' + currentCampaign
                    )
                    if (slide) {
                        slide.classList.remove(hideClass)
                    } else {
                        slides.innerHTML +=
                            '<div id="slide-' +
                            currentCampaign +
                            '" class="slide" style="background-image: url(' +
                            campaign.image +
                            ');"></div>'
                    }
                    setTimeout(updateSlide, campaign.slide_duration * 1000)

                    previousWasVideo = false
                } else {
                    youtubePlayer.loadVideoById(campaign.video, 'highres')
                    youtubePlayer.playVideo()

                    player.classList.remove(hideClass)

                    previousWasVideo = true
                }
                currentCampaign++
            }
        }

        window.addEventListener('load', (_) => {
            updateCampaigns()
            const everyTwoHours = 60 * 60 * 2 * 1000
            setInterval(updateCampaigns, everyTwoHours)
        })
    </script>
@endpush
