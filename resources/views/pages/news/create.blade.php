@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/news') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Caption English</label>
                                <input type="text" name="caption_en" id="caption_en" class="form-control" required placeholder="Caption English" value="{{ old('caption_en') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Caption Arabic</label>
                                <input type="text" name="caption_ar" id="caption_ar" class="form-control" required placeholder="Caption Arabic" value="{{ old('caption_ar') }}">
                            </div>
                            {{--<div class="form-group col-sm-3">--}}
                                {{--<label>Shop Restaurant Code (if you want to link the event to an item in the shop)</label>--}}
                                {{--<input type="text" name="code" id="code" class="form-control" placeholder="Code" value="{{ old('code') }}">--}}
                            {{--</div>--}}
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Description English</label>
                                <textarea rows="3" name="description_en" class="form-control">{{old('description_en')}}</textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Description Arabic</label>
                                <textarea rows="3" name="description_ar" class="form-control">{{old('description_ar')}}</textarea>
                            </div>
                            <div class="form-group col-sm4">
                                <label>News DateTime</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="item_datetime" id="item_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Image (Aspect Ratio : 2.34)</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="form-group col-sm-3">--}}
                                {{--<label>Youtube ID</label>--}}
                                {{--<input type="text" name="youtube_id" id="youtube_id" class="form-control" placeholder="Youtube ID" value="{{ old('youtube_id') }}">--}}
                            {{--</div>--}}
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $('form').submit(function () {

            // Get the Login Name value and trim it
            var image = $.trim($('#image').val());
            var youtube_id = $.trim($('#youtube_id').val());
            // Check if empty of not
            if (image != '' && youtube_id != '') {
                alert('you must fill one of 2 fields: Image or Youtube ID');
                return false;
            }

            if (image == '' && youtube_id == '') {
                alert('you must fill one of 2 fields: Image or Youtube ID');
                return false;
            }
        });
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
        tinymce.init({
            selector: 'textarea',
            height: 200,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
            ],
            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist outdent indent | link',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    </script>
@endsection
