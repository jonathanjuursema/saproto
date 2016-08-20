@extends('website.layouts.panel')

@section('page-title')
    Print Something
@endsection

@section('panel-title')
    What would you like to print today? Think about the trees!
@endsection

@section('panel-body')

    <form method="post" action="{{ route("print::print") }}" enctype="multipart/form-data">

        {!! csrf_field() !!}

        <div class="row">

            <div class="col-md-12">

                <p>
                    Here you can upload something to print. Printing something costs €{{ number_format($price, 2) }} per
                    page. Prints are printed single-sided, on A4 paper, in black. You can collect your prints in the
                    Protopolis. <strong>Only PDF files can be submitted for printing.</strong> Documents uploaded using
                    this form are deleted every night.
                </p>

                <div class="form-group">
                    <div class="col-md-8">
                        <input type="file" class="form-control" name="file"
                               placeholder="Select your file to be uploaded." required>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group">
                            <input class="form-control" type="number" name="copies" value="1" min="1">
                            <span class="input-group-addon">copies</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @endsection

        @section('panel-footer')

            @if (Auth::user()->can('board'))
                <div class="checkbox pull-left">
                    <label>
                        <input type="checkbox" name="free"> I'd like to print for free!
                    </label>
                </div>
            @endif

            <button type="submit" class="btn btn-success pull-right" style="margin-left: 15px;">Submit</button>

            <a href="#" onclick="javascript:history.go(-1)" class="btn btn-default pull-right">Cancel</a>

    </form>

@endsection