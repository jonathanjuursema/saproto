@extends('website.layouts.redesign.dashboard')
@php($newsName=$is_weekly ? 'weekly' : 'news')
@section('page-title')
    @if($new)
        Create a new {{ $newsName }}
    @else
        Edit {{ $item->title }}
    @endif

    @if($is_weekly && $lastWeekly)
        <a class="badge bg-danger disabled float-end">
            Last sent: {{ Carbon::parse($lastWeekly->published_at)->diffForHumans() }}
        </a>
    @endif
@endsection

@section('container')

    <form method="post"
          action="@if($new) {{ route("news::store") }} @else {{ route("news::update", ['id' => $item->id]) }} @endif"
          enctype="multipart/form-data">

        @csrf

        <div class="row justify-content-center">

            <div class="col-md-4">

                <div class="card mb-3">

                    <div class="card-header bg-dark text-white">
                        @yield('page-title')
                    </div>

                    <div class="card-body">
                        @if(!$is_weekly)
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       placeholder="Revolutionary new activity!" value="{{ $item->title ?? '' }}"
                                       required>
                            </div>

                            @include('components.forms.datetimepicker', [
                                'name' => 'published_at',
                                'label' => 'Publish at:',
                                'placeholder' => $item ? strtotime($item->published_at) : strtotime(Carbon::now())
                            ])
                        @endif

                        <div class="form-group">
                            <label for="editor">Content</label>
                            @include('components.forms.markdownfield', [
                                'name' => 'content',
                                'placeholder' => 'Text goes here.',
                                'value' => $item ? $item->content : null
                            ])
                        </div>

                    </div>


                </div>

            </div>

            <div class="col-md-5">

                <div class="row">
                    @include('news.includes.activities')
                    @if($is_weekly && $item?->featuredImage)
                        <div class="card mb-3">
                            <div class="card-header">
                                Replace image
                            </div>
                            <div class="card-body">
                                <div class="custom-file">
                                    <input id="featured-image" type="file" class="form-control" name="image">
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card mb-3">
                        <div class="card-body">
                            <a href="{{ route("news::admin") }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-success float-end">Save as {{$newsName}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                @if($is_weekly && !$new)
                    <div class="row" style="height:75vh">
                        <div class="card mb-3 p-0">
                            <div class="card-header">
                                Weekly Preview:
                            </div>
                            <div class="card-body px-0">
                                <div class="ratio ratio-21x9 h-100">
                                    <iframe src="{{route('news::showWeeklyPreview', ['id'=>$item->id])}}"
                                            title="W3Schools Free Online Web Tutorials"></iframe>
                                </div>
                            </div>

                            <div class="card-footer">
                                @if(!$item->published_at)
                                    @include('components.modals.confirm-modal', [
                                               'action' => route('news::sendWeekly', ['id' => $item->id]),
                                               'text' => 'Send weekly!',
                                               'title' => 'Confirm Sending Weekly',
                                               'classes' => 'btn ms-2 '.(Carbon::parse($lastWeekly->published_at)->diffInDays(Carbon::now()) < 7 ? 'btn-danger' : 'btn-success'),
                                               'message' => 'Are you sure you want to send this weekly? <br> It was last sent: <b>'.Carbon::parse($lastWeekly->published_at)->diffForHumans()."</b> and should only be sent once per week.<br> This will send an email to everyone on the list.",
                                               'confirm' => 'Send',
                                           ])
                                @else
                                    This weekly has already been sent!
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="card mb-3 p-0">
                            <div class="card-header">
                                Featured image
                            </div>
                            @if($item?->featuredImage)
                                <img src="{!! $item->featuredImage->generateImagePath(700,null) !!}" width="100%;"
                                     class="card-img-top">
                            @endif
                            <div class="card-body">
                                <div class="custom-file">
                                    <input id="featured-image" type="file" class="form-control" name="image">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>

@endsection
