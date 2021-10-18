@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/locate_us') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Title" value="{{ old('name') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Address</label>
                                <textarea rows="3" name="description" class="form-control" placeholder="Address">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" required placeholder="Latitude" value="{{ old('latitude') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" required placeholder="Longitude" value="{{ old('longitude') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Category Image</label>
                                <!-- bootstrap-imageupload. -->
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Upload Image</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <!-- The file is stored here. -->
                                            <input type="file" name="image-file">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                    <!-- bootstrap-imageupload method buttons. -->
                                    {{--<button type="button" id="imageupload-disable" class="btn btn-danger">Disable</button>--}}
                                    {{--<button type="button" id="imageupload-enable" class="btn btn-success">Enable</button>--}}
                                    {{--<button type="button" id="imageupload-reset" class="btn btn-warning">Reset</button>--}}
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.locate_us.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();

//        $('#imageupload-disable').on('click', function() {
//            $imageupload.imageupload('disable');
//            $(this).blur();
//        })
//
//        $('#imageupload-enable').on('click', function() {
//            $imageupload.imageupload('enable');
//            $(this).blur();
//        })
//
//        $('#imageupload-reset').on('click', function() {
//            $imageupload.imageupload('reset');
//            $(this).blur();
//        });
        //    tinymce.init({
        //        selector: 'textarea',
        //        height: 500,
        //        menubar: true,
        //        plugins: [
        //            'advlist autolink lists link image charmap print preview anchor',
        //            'searchreplace visualblocks code fullscreen',
        //            'insertdatetime media table contextmenu paste code'
        //        ],
        //        toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        //        content_css: '//www.tinymce.com/css/codepen.min.css'
        //    });
//        tinymce.init({
//            selector: 'textarea',
//            height: 200,
//            menubar: false,
//            plugins: [
//            ],
//            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
//            content_css: '//www.tinymce.com/css/codepen.min.css'
//        });
    </script>
@endsection
