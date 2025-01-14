@extends('website.layouts.content')

@section('container')
    <div id="container" class="container container-nobg">
        <div class="col-md-6 col-md-offset-3 col-xs-12 col-xs-offset-0">
            @include('website.announcements')

            <div class="panel panel-default">
                <div class="panel-heading">
                    @section('panel-title')
                        
                    @endsection

                    @yield('panel-title')
                </div>
                <div class="panel-body clearfix">
                    @section('panel-body')
                        
                    @endsection

                    @yield('panel-body')
                </div>
                <div class="panel-footer clearfix">
                    @section('panel-footer')
                        
                    @endsection

                    @yield('panel-footer')
                </div>
            </div>
        </div>
    </div>
@endsection
