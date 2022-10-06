@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/categories') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Type?</label>
                                <input name="type" type="checkbox" data-toggle="toggle" data-on="Service" data-off="Product" data-onstyle="success" data-offstyle="danger" data-size="large">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Name English</label>
                                <input type="text" name="name_en" required id="name_en" class="form-control" placeholder="Name English" value="{{ old('name_en') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Price in AED</label>
                                <input type="text" name="price" required id="price" class="form-control" placeholder="Price" value="{{ old('price') }}">
                            </div>
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Name Arabic</label>--}}
{{--                                <input type="text" name="name_ar" required id="name_ar" class="form-control" placeholder="Name Arabic" value="{{ old('name_ar') }}">--}}
{{--                            </div>--}}
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
                                    <div class="url-tab panel-body">
                                        <div class="input-group">
                                            <input type="text" class="form-control hasclear" placeholder="Image URL">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default">Submit</button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        <!-- The URL is stored here. -->
                                        <input type="hidden" name="image-url">
                                    </div>
                                    <!-- bootstrap-imageupload method buttons. -->
{{--                                    <button type="button" id="imageupload-disable" class="btn btn-danger">Disable</button>--}}
{{--                                    <button type="button" id="imageupload-enable" class="btn btn-success">Enable</button>--}}
{{--                                    <button type="button" id="imageupload-reset" class="btn btn-warning">Reset</button>--}}
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Category Selected Image</label>
                                <!-- bootstrap-imageupload. -->
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Upload Selected Image</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <!-- The file is stored here. -->
                                            <input type="file" name="image-file1">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                    <div class="url-tab panel-body">
                                        <div class="input-group">
                                            <input type="text" class="form-control hasclear" placeholder="Image URL">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-default">Submit</button>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        <!-- The URL is stored here. -->
                                        <input type="hidden" name="image-url">
                                    </div>
                                    <!-- bootstrap-imageupload method buttons. -->
                                    {{--                                    <button type="button" id="imageupload-disable" class="btn btn-danger">Disable</button>--}}
                                    {{--                                    <button type="button" id="imageupload-enable" class="btn btn-success">Enable</button>--}}
                                    {{--                                    <button type="button" id="imageupload-reset" class="btn btn-warning">Reset</button>--}}
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-dark">Back</a>
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
