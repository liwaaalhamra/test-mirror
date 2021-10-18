@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.items.store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-md-4">--}}
                                {{--<label>Gift or Shop Restaurant ?</label>--}}
                                {{--<input name="is_gift" type="checkbox" data-toggle="toggle" data-on="Gift"  data-off="Shop Restaurant" data-onstyle="success" data-size="large">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">
                            {{--<div class="form-group col-sm-4">--}}
                                {{--<label>SKU</label>--}}
                                {{--<input type="text" name="code" id="code" class="form-control" required placeholder="SKU" value="{{ old('code') }}">--}}
                            {{--</div>--}}
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Category</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" required title="Choose one of the following..." id="category_id" data-live-search="true" name="category_id" data-actions-box="true">
                                        @foreach($categories as $category)
                                            <option data-tokens="{{$category->name_en}}" value="{{$category->id}}" >{{ucwords($category->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Choose Menu Category</label>
                                <select class="form-control selectpicker" title="Choose one of the following..." disabled  data-live-search="true" id="sub_category_id" name="sub_category_id">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="Name English" value="{{ old('name_en') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" required placeholder="Name Arabic" value="{{ old('name_ar') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Share Link</label>
                                <input type="url" name="share_link" id="share_link" class="form-control" placeholder="Share Link" value="{{ old('share_link') }}">
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-sm-3">
                                <label>Unit Type</label>
                                <input type="text" name="unit_type" id="unit_type" class="form-control" placeholder="Unit Type" value="{{ old('unit_type') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Unit Value</label>
                                <input type="text" name="unit_value" id="unit_value" class="form-control" placeholder="Unit Value" value="{{ old('unit_value') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Expiry Date</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="item_datetime" id="item_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Price</label>
                                <input type="number" name="price" step="any" id="price" required class="form-control" placeholder="Price" value="{{ old('price') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Discounted Price</label> <small style="color: red">(leave it blank if no discount applied)</small>
                                <input type="number" name="discounted_price" step="any" id="discounted_price" class="form-control" placeholder="Discounted Price" value="{{ old('discounted_price') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Quantity</label>
                                <input type="number" name="quantity" step="any" id="quantity" required class="form-control" placeholder="Quantity" value="{{ old('quantity') }}">
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-sm-4">
                                <label>Description English</label>
                                <textarea name="description_en" id="description_en" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="description_ar" class="form-control"></textarea>
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
                        <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $('#category_id').on('change', function () {
            if (this.value != '') {
                param = '/' + this.value ;
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: location.href.split("items")[0]+ 'get_menu_categories' + param,
                    success: function (response) {
                        $("#sub_category_id").prop('disabled', false);
                        $('#sub_category_id').empty();
                        $.each(response, function (i, value) {
                            $('#sub_category_id').append($('<option>').text(value).attr('value', i));
                        });
                        $('#sub_category_id').selectpicker('refresh');
                    }
                });

            } else {
                $("#sub_category_id").prop('disabled', true);
                $('#sub_category_id').selectpicker('refresh');
            }
        });
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
        CKEDITOR.replace( 'description_en' );
        CKEDITOR.replace( 'description_ar' );
    </script>

@endsection
