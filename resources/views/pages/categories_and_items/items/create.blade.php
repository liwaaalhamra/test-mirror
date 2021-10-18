@extends('vendor.layout')

@section('content')
    <link rel="stylesheet" href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" />

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Items of {{ucwords($category->name)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ secure_url('admin/categories/'.$category->id.'/items') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label" >Types</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" id="type_id" data-live-search="true" name="type_id" data-actions-box="true">
                                        <option data-tokens="" value="" selected>Choose...</option>
                                        @foreach($types as $id => $value)
                                            <option data-tokens="{{$value}}" value="{{$id}}" > {{$value}}</option>
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
                                            <option data-tokens="{{$value}}" value="{{$id}}" > {{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ old('name') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Price</label>
                                <input type="number" name="price" step="any" id="price" required class="form-control" placeholder="Price" value="{{ old('price') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Dosage</label>
                                <input type="text" name="dosage" id="dosage" required class="form-control" placeholder="Dosage" value="{{ old('dosage') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-8">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
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

                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.categories.items.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
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
            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist outdent indent | link',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    </script>
@endsection
