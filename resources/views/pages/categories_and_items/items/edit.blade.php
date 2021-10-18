@extends('vendor.layout')

@section('content')
    <link rel="stylesheet" href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" />


    <div class="row">
        <form id="submit_form" action="{{ secure_url('admin/categories/'.$category->id.'/items/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Update Item of {{ucwords($category->name)}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label" >Types</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" id="type_id" data-live-search="true" name="type_id" data-actions-box="true">
                                        <option data-tokens="" value="" selected>Choose...</option>
                                        @foreach($types as $id => $value)
                                            <option data-tokens="{{$value}}" @if($item->type_id == $id) selected @endif value="{{$id}}" > {{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label" >Brands</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" id="brand_id" data-live-search="true" name="brand_id" data-actions-box="true">
                                        <option data-tokens="" value="" selected>Choose...</option>
                                        @foreach($brands as $id => $value)
                                            <option data-tokens="{{$value}}" @if($item->brand_id == $id) selected @endif value="{{$id}}" > {{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name</label>
                                <input type="text" name="name" id="name" required class="form-control" placeholder="Name" value="{{ $item->name }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Price</label>
                                <input type="number" name="price" required step="any" id="price" class="form-control" placeholder="Price" value="{{ $item->price }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Dosage</label>
                                <input type="text" name="dosage" id="dosage" required class="form-control" placeholder="Dosage" value="{{ $item->dosage }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-8">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control">{{$item->description}}</textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->getMediaName() != null)
                                            <a href="{{secure_url('admin/categories/items/'.$item->id.'/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $item->image }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="logo" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Image</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{ $item->image }}" width="100%;">
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

                        <div class="form-group col-sm-12">
                            <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                            <a href="{{ route('admin.categories.items.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>


    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });

    </script>
    <script>
        tinymce.init({
            selector: 'textarea',
            height: 200,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
            ],
            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    </script>
@endsection
