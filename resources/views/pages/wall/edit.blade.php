@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/wall/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Choose Department</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="department" required>
                                    @foreach($departments as $department)
                                        <option data-tokens="{{$department}}" value="{{ $department }}" @if($department == $item->user_name) selected @endif>{{ ucwords($department) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Title</label>
                                <input type="text" name="title" id="title" class="form-control" required placeholder="Title" value="{{ $item->title }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>URL</label>
                                <input type="text" name="url" id="url" class="form-control" placeholder="URL" value="{{ $item->post_source_url }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description</label>
                                <textarea rows="3" name="description" class="form-control" placeholder="Description">{{ $item->description }}</textarea>
                            </div>
                            <div class="form-group col-sm-3">
                                <div class="imageupload panel panel-default">

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="icon" id="icon">

                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->getUserImageName() != null)
                                            <a href="{{secure_url('admin/wall/'.$item->id.'/image/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Icon</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Icon</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->getUserImageName() != null)
                                                <img  alt="Image Preview"  src="{{ $item->user_image }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="logo" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Icon</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $item->user_image }}" width="100%;">
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
                            <div class="form-group col-sm-3">
                                <div class="imageupload panel panel-default">

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->media()->where('type','=','image')->latest()->first() != null && $item->media()->where('type','=','image')->latest()->first()->getUrlName() != null)
                                            <a href="{{secure_url('admin/wall/'.$item->id.'/icon/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->media()->where('type','=','image')->latest()->first() != null && $item->media()->where('type','=','image')->latest()->first()->getUrlName() != null)
                                                <img  alt="Image Preview"  src="{{ $item->media()->where('type','=','image')->latest()->first()->url }}" class="thumbnail" data-toggle="modal" data-target="#icon"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="icon" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $item->media()->where('type','=','image')->latest()->first()->url }}" width="100%;">
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
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Media Type</label>
                                <input name="post_media_type" id="post_media_type" type="checkbox" @if($item->post_media_type == 'image') checked @endif data-toggle="toggle" data-on="Image" data-off="Video" data-onstyle="success" data-offstyle="primary" data-size="large">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.wall.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
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
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
