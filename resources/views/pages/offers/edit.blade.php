@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/offers/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Item</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" id="item_id" data-live-search="true" name="item_id" data-actions-box="true">
                                        <option data-tokens="" value="" selected>Choose...</option>
                                        @foreach($items as $single_item)
                                            <option data-tokens="{{$single_item->name_en}}" @if($item->item_id == $single_item->id) selected @endif value="{{$single_item->id}}" >{{ucwords($single_item->name_en)}}</option>
                                        @endforeach
                                        {{--@foreach($sub_categories as $sub_category)--}}
                                            {{--<optgroup label="{{ucwords($sub_category->category->name_en)}} - {{ucwords($sub_category->name_en)}}">--}}
                                                {{--@foreach($sub_category->items as $single_item)--}}
                                                    {{--<option data-tokens="{{$single_item->name}}" @if($item->item_id == $single_item->id) selected @endif value="{{$single_item->id}}" >{{ucwords($single_item->name_en)}} {!! ucwords($single_item->description_en) !!} - {{ucwords($single_item->unit_value)}} {{ucwords($single_item->unit_type)}} - {{ucwords($single_item->price)}} KWD</option>--}}
                                                {{--@endforeach--}}
                                            {{--</optgroup>--}}
                                        {{--@endforeach--}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Link</label>
                                <input type="text" name="link" id="link" class="form-control" placeholder="Link" value="{{ $item->link }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Title English</label>
                                <input type="text" name="title_en" id="title_en" class="form-control" required placeholder="Title English" value="{{ $item->title_en }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Title Arabic</label>
                                <input type="text" name="title_ar" id="title_ar" class="form-control" required placeholder="Title Arabic" value="{{ $item->title_ar }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>SubTitle English</label>
                                <input type="text" name="subtitle_en" id="subtitle_en" class="form-control" required placeholder="SubTitle English" value="{{ $item->subtitle_en }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>SubTitle Arabic</label>
                                <input type="text" name="subtitle_ar" id="subtitle_ar" class="form-control" required placeholder="SubTitle Arabic" value="{{ $item->subtitle_ar }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="text" name="item_order" required id="item_order" class="form-control" placeholder="Order" value="{{ $item->item_order }}">
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
                                            <a href="{{secure_url('admin/offers/'.$item->id.'/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
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

                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
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
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
