@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/wall') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Choose Department</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="department" required>
                                    @foreach($departments as $item)
                                        <option data-tokens="{{$item}}" value="{{ $item }}">{{ ucwords($item) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Title</label>
                                <input type="text" name="title" id="title" class="form-control" required placeholder="Title" value="{{ old('title') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>URL</label>
                                <input type="text" name="url" id="url" class="form-control" placeholder="URL" value="{{ old('url') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description</label>
                                <textarea rows="3" name="description" class="form-control" placeholder="Description">{{ old('description') }}</textarea>
                            </div>
                            <div class="form-group col-sm-3">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Image (Aspect Ratio : 1.2)</h3>
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
                            <div class="form-group col-sm-3">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Icon (Aspect Ratio : 1)</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="icon" id="icon">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Media Type</label>
                                <input name="post_media_type" id="post_media_type" type="checkbox" data-toggle="toggle" data-on="Image" data-off="Video" data-onstyle="success" data-offstyle="primary" data-size="large">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.wall.index') }}" class="btn btn-sm btn-dark">Back</a>
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
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
        // tinymce.init({
        //     selector: 'textarea',
        //     height: 200,
        //     menubar: false,
        //     plugins: [
        //         'advlist autolink lists link image charmap print preview anchor',
        //     ],
        //     toolbar: 'undo redo  | styleselect | bold italic | bullist numlist outdent indent | link',
        //     content_css: '//www.tinymce.com/css/codepen.min.css'
        // });
    </script>
@endsection
