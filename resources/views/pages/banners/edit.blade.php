@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/banners/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-5">--}}
                                {{--<label>Caption English</label>--}}
                                {{--<input type="text" name="caption_en" id="caption_en" class="form-control" required placeholder="Caption English" value="{{ $item->caption_en }}">--}}
                            {{--</div>--}}
                            {{--<div class="form-group col-sm-5">--}}
                                {{--<label>Caption Arabic</label>--}}
                                {{--<input type="text" name="caption_ar" id="caption_ar" class="form-control" required placeholder="Caption Arabic" value="{{ $item->caption_ar }}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Link</label>
                                <input type="url" name="link" id="link" class="form-control" placeholder="Link" value="{{ $item->banner_link }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="text" name="item_order" required id="item_order" class="form-control" placeholder="Order" value="{{ $item->banner_order }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->getMediaName() != null)
                                            <a href="{{secure_url('admin/banners/'.$item->id.'/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Image (Aspect Ratio : 2.34)</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $item->banner_image }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="logo" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $item->banner_image }}" width="100%;">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <br><br>
                                            @endif
                                        </div>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $('form').submit(function () {

            // Get the Login Name value and trim it
            var image = $.trim($('#image').val());
            var youtube_id = $.trim($('#youtube_id').val());
            var existing_image = '{{$item->getMediaName()}}';
            // Check if empty of not
            if (image != '' && youtube_id != '') {
                alert('you must fill one of 2 fields: Image or Youtube ID');
                return false;
            }

            if (image == '' && existing_image == '' && youtube_id == '') {
                alert('you must fill one of 2 fields: Image or Youtube ID');
                return false;
            }
        });
        $(function () {
            var item_datetime = "{{$item->news_datetime}}";
            if(item_datetime == ''){
                $('#datetimepicker1').datetimepicker();
            }else{
                var item_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->news_datetime)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker1').datetimepicker({
                    defaultDate: item_datetime_formatted
                });
            }
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
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
