@extends('website.layouts.redesign.generic')

@section('page-title')
    News
@endsection

@section('container')
    @can('board')
        <div class="w-100 mb-3">
            <a href="{{ route('news::admin') }}" class="btn btn-info w-100">
                <i class="fas fa-edit"></i>
                <span class="d-none d-sm-inline">News admin</span>
            </a>
        </div>
    @endcan

    <div class="row">
        <div class="col">
            @include('news.includes.newsitemcolumn', ['items' => $newsitems, 'text' => 'News Articles'])
        </div>
    </div>
@endsection
