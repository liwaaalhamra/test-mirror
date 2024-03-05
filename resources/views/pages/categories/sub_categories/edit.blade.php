@extends('vendor.layout')

@section('content')
    <div class="row">
        <form id="submit_form" action="{{ secure_url('admin/categories/'.$category->id.'/sub_categories/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Update Item of {{ucwords($category->name)}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Is Featured?</label>
                                <input name="is_featured" type="checkbox" @if($item->is_featured) checked @endif data-toggle="toggle" data-on="YES" data-off="NO" data-onstyle="success" data-offstyle="danger" data-size="large">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="text" name="item_order" required id="item_order" class="form-control" placeholder="Order" value="{{ $item->item_order }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" required id="name_en" class="form-control" placeholder="Name English" value="{{ $item->name_en }}">
                            </div>
                            {{-- <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" required id="name_ar" class="form-control" placeholder="Name Arabic" value="{{ $item->name_ar }}">
                            </div> --}}

                        </div>
                        {{-- <div class="row">

                            <div class="form-group col-sm-6">
                                <label>Description English</label>
                                <textarea name="description_en" id="desc_en" rows="10" class="form-control">{{ $item->description_en }}</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="desc_ar" rows="10" class="form-control">{{ $item->description_ar }}</textarea>
                            </div>
                        </div> --}}
                        {{-- <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="image" id="image">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->getMediaName() != null)
                                            <a href="{{secure_url('admin/categories/sub_categories/'.$item->id.'/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
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
                        </div> --}}

                        <div class="form-group col-sm-12">
                            <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                            <a href="{{ route('admin.categories.sub_categories.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        CKEDITOR.replace( 'desc_en' );
        CKEDITOR.replace( 'desc_ar' );
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
            allowedFormats: ['jpg', 'jpeg', 'png', 'gif'],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });

    </script>
@endsection
