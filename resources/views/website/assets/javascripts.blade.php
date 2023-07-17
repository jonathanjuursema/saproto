{{-- Blade variable can be accessed through this config object. --}}
<script type="text/javascript" nonce="{{ csp_nonce() }}">
    const config = {
        routes: {
            api_search_user: "{{ route('api::search::user') }}",
            api_search_event: "{{ route('api::search::event') }}",
            api_search_product: "{{ route('api::search::product') }}",
            api_search_committee: "{{ route('api::search::committee') }}",
            api_search_achievement: "{{ route('api::search::achievement') }}",
            api_omnomcom_stock: "{{ route('api::omnomcom::stock') }}",
            api_wallstreet_active: "{{ route('api::wallstreet::active') }}",
        },
        analytics_url: "{{ config('proto.analytics_url') }}",
        discord_server_id: "{{ config('proto.discord_server_id') }}",
        theme: "{{ Auth::check() && Auth::user()->theme !== null ? config('proto.themes')[Auth::user()->theme] : 'light' }}",
        @isset($companies) company_count: {{ count($companies) }} @endisset
    }
</script>

@vite('resources/assets/js/application.js')

<script src="https://kit.fontawesome.com/63e98a7060.js" crossorigin="anonymous"></script>