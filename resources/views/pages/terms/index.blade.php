@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                    <form action="{{ secure_url('admin/terms') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Terms English</label>
                                <textarea name="description_en" rows="5" id="desc_en" class="form-control">@if(!empty($terms)) {{ $terms->description_en }} @endif</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Terms Arabic</label>
                                <textarea name="description_ar" rows="5" id="desc_ar" class="form-control">@if(!empty($terms)) {{ $terms->description_ar }} @endif</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Privacy English</label>
                                <textarea name="privacy_en" rows="5" id="privacy_desc_en" class="form-control">@if(!empty($privacy)) {{ $privacy->description_en }} @endif</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Privacy Arabic</label>
                                <textarea name="privacy_ar" rows="5" id="privacy_desc_ar" class="form-control">@if(!empty($privacy)) {{ $privacy->description_ar }} @endif</textarea>
                            </div>
                        </div>
{{--                        <div class="row">--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Restaurant Terms English</label>--}}
{{--                                <textarea name="restaurant_description_en" rows="5" id="restaurant_desc_en" class="form-control">@if(!empty($restaurant_terms)) {{ $restaurant_terms->description_en }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Restaurant Terms Arabic</label>--}}
{{--                                <textarea name="restaurant_description_ar" rows="5" id="restaurant_desc_ar" class="form-control">@if(!empty($restaurant_terms)) {{ $restaurant_terms->description_ar }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="row">--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Restaurant Privacy English</label>--}}
{{--                                <textarea name="restaurant_privacy_en" rows="5" id="restaurant_privacy_desc_en" class="form-control">@if(!empty($restaurant_privacy)) {{ $restaurant_privacy->description_en }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Restaurant Privacy Arabic</label>--}}
{{--                                <textarea name="restaurant_privacy_ar" rows="5" id="restaurant_privacy_desc_ar" class="form-control">@if(!empty($restaurant_privacy)) {{ $restaurant_privacy->description_ar }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="row">--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Supplier Terms English</label>--}}
{{--                                <textarea name="supplier_description_en" rows="5" id="supplier_desc_en" class="form-control">@if(!empty($supplier_terms)) {{ $supplier_terms->description_en }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Supplier Terms Arabic</label>--}}
{{--                                <textarea name="supplier_description_ar" rows="5" id="supplier_desc_ar" class="form-control">@if(!empty($supplier_terms)) {{ $supplier_terms->description_ar }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="row">--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Supplier Privacy English</label>--}}
{{--                                <textarea name="supplier_privacy_en" rows="5" id="supplier_privacy_desc_en" class="form-control">@if(!empty($supplier_privacy)) {{ $supplier_privacy->description_en }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-sm-6">--}}
{{--                                <label>Supplier Privacy Arabic</label>--}}
{{--                                <textarea name="supplier_privacy_ar" rows="5" id="supplier_privacy_desc_ar" class="form-control">@if(!empty($supplier_privacy)) {{ $supplier_privacy->description_ar }} @endif</textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-4">--}}
                                {{--<div class="imageupload panel panel-default">--}}

                                    {{--<div class="file-tab panel-body">--}}
                                        {{--<label class="btn btn-default btn-file">--}}
                                            {{--<span>Browse</span>--}}
                                            {{--<input type="file" name="image" id="image">--}}
                                        {{--</label>--}}
                                        {{--<button type="button" class="btn btn-default">Remove</button>--}}
                                        {{--@if(!empty($terms)  && $terms->getImageName() != null)--}}
                                            {{--<a href="{{secure_url('admin/terms/delete')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>--}}
                                        {{--@endif--}}
                                    {{--</div>--}}
                                    {{--<div class="panel-heading clearfix" style="text-align: center">--}}
                                        {{--<h3 class="panel-title pull-left">Image</h3>--}}
                                        {{--<div class="btn-group">--}}
                                            {{--@if(!empty($terms)  && $terms->getImageName() != null)--}}
                                                {{--<img  alt="Image Preview"  src="{{ $terms->image }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>--}}
                                                {{--<div id="logo" class="modal fade" role="dialog">--}}
                                                    {{--<div class="modal-dialog">--}}
                                                        {{--<div class="modal-content">--}}
                                                            {{--<div class="modal-header">--}}
                                                                {{--<button type="button" class="close" data-dismiss="modal">&times;</button>--}}
                                                                {{--<h3 class="modal-title">Image</h3>--}}
                                                            {{--</div>--}}
                                                            {{--<div class="modal-body">--}}
                                                                {{--<img src="{{ $terms->image }}" width="100%;">--}}
                                                            {{--</div>--}}
                                                            {{--<div class="modal-footer">--}}
                                                                {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                                                            {{--</div>--}}
                                                        {{--</div>--}}

                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<br><br>--}}
                                            {{--@endif--}}
                                        {{--</div>--}}
                                        {{--<div class="btn-group pull-right">--}}
                                            {{--<button type="button" class="btn btn-default active">File</button>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </form>
                    @endif
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
        CKEDITOR.replace( 'desc_en' );
        CKEDITOR.replace( 'supplier_desc_en' );
        CKEDITOR.replace( 'restaurant_desc_en' );
        CKEDITOR.replace( 'desc_ar' );
        CKEDITOR.replace( 'supplier_desc_ar' );
        CKEDITOR.replace( 'restaurant_desc_ar' );
        CKEDITOR.replace( 'privacy_desc_ar' );
        CKEDITOR.replace( 'supplier_privacy_desc_ar' );
        CKEDITOR.replace( 'restaurant_privacy_desc_ar' );
        CKEDITOR.replace( 'privacy_desc_en' );
        CKEDITOR.replace( 'supplier_privacy_desc_en' );
        CKEDITOR.replace( 'restaurant_privacy_desc_en' );
    </script>
@endsection