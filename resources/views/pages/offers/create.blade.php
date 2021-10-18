@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/offers') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Item</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" id="item_id" data-live-search="true" name="item_id" data-actions-box="true">
                                        <option data-tokens="" value="" selected>Choose...</option>
                                        @foreach($items as $single_item)
                                            <option data-tokens="{{$single_item->name_en}}" value="{{$single_item->id}}" >{{ucwords($single_item->name_en)}}</option>
                                        @endforeach
                                        {{--@foreach($sub_categories as $sub_category)--}}
                                            {{--<optgroup label="{{ucwords($sub_category->category->name_en)}} - {{ucwords($sub_category->name_en)}}">--}}
                                                {{--@foreach($sub_category->items as $single_item)--}}
                                                    {{--<option data-tokens="{{$single_item->name}}" value="{{$single_item->id}}" >{{ucwords($single_item->name_en)}} {!! ucwords($single_item->description_en) !!} - {{ucwords($single_item->unit_value)}} {{ucwords($single_item->unit_type)}} - {{ucwords($single_item->price)}} KWD</option>--}}
                                                {{--@endforeach--}}
                                            {{--</optgroup>--}}
                                        {{--@endforeach--}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Link</label>
                                <input type="text" name="link" id="link" class="form-control" placeholder="Link" value="{{ old('link') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title English</label>
                                <input type="text" name="title_en" id="title_en" class="form-control" required placeholder="Title English" value="{{ old('title_en') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Title Arabic</label>
                                <input type="text" name="title_ar" id="title_ar" class="form-control" required placeholder="Title Arabic" value="{{ old('title_ar') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>SubTitle English</label>
                                <input type="text" name="subtitle_en" id="subtitle_en" class="form-control" required placeholder="SubTitle English" value="{{ old('subtitle_en') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>SubTitle Arabic</label>
                                <input type="text" name="subtitle_ar" id="subtitle_ar" class="form-control" required placeholder="SubTitle Arabic" value="{{ old('subtitle_ar') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Image</h3>
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
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-sm btn-dark">Back</a>
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
