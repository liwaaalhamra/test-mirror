@extends('vendor.layout')

@section('content')
    <div class="alert alert-danger" style="display: none">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <p id="errors"></p>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.restaurants.store') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Username</label>
                                <input type="text" id="username" name="username" class="form-control" >
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Password</label>
                                <input type="password" id="password" name="password" class="form-control">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                            </div>
                            <div class="form-group col-sm-2">
                                <small style="color: red">
                                Password must be at least 8 characters <br>
                                must contain at least one lowercase letter<br>
                                one uppercase letter<br>
                                one digit<br>
                                a special character<br>
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Is Partner ?</label>
                                <input name="is_partner" type="checkbox" data-toggle="toggle" data-on="YES"  data-off="NO" data-onstyle="success" data-size="large">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Is Closed ?</label>
                                <input name="is_closed" type="checkbox" data-toggle="toggle" data-on="YES"  data-off="NO" data-onstyle="success" data-size="large">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Has Table Reservation ?</label>
                                <input name="has_reserve_table" type="checkbox" data-toggle="toggle" data-on="YES"  data-off="NO" data-onstyle="success" data-size="large">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Has Delivery ?</label>
                                <input name="has_delivery" type="checkbox" data-toggle="toggle" data-on="YES"  data-off="NO" data-onstyle="success" data-size="large">
                            </div>
                        </div>
                        <div class="row">
                            {{--<div class="form-group col-md-4">--}}
                                {{--<label class="control-label" >Choose Category</label>--}}
                                {{--<div class="form-group">--}}
                                    {{--<select class="form-control selectpicker" id="sub_category_id" data-live-search="true" name="sub_category_id" data-actions-box="true">--}}
                                        {{--<option data-tokens="" value="" selected>Choose...</option>--}}
                                        {{--@foreach($sub_categories as $sub_category)--}}
                                            {{--<option data-tokens="{{$sub_category->name}}" value="{{$sub_category->id}}" >{{ucwords($sub_category->category->name_en)}} - {{ucwords($sub_category->name_en)}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Category</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" title="Choose one of the following..." id="category_id" data-live-search="true" name="category_id" data-actions-box="true">
                                        @foreach($categories as $category)
                                            <option data-tokens="{{$category->name_en}}" value="{{$category->id}}" >{{ucwords($category->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Choose Collection</label><small style="color: red">(Optional)</small>
                                <select class="form-control selectpicker" title="Choose one of the following..." disabled  data-live-search="true" id="sub_category_id" name="sub_category_id">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Tags</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" title="Choose one of the following..." id="tag_ids" data-live-search="true" name="tag_ids[]" multiple data-actions-box="true">
                                        @foreach($tags as $tag)
                                            <option data-tokens="{{$tag->name_en}}" value="{{$tag->id}}" >{{ucwords($tag->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Cuisines</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" title="Choose one of the following..." id="cuisine_ids" data-live-search="true" name="cuisine_ids[]" multiple data-actions-box="true">
                                        @foreach($cuisines as $cuisine)
                                            <option data-tokens="{{$cuisine->name_en}}" value="{{$cuisine->id}}" >{{ucwords($cuisine->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Top Cravings</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" title="Choose one of the following..." id="craving_ids" data-live-search="true" name="craving_ids[]" multiple data-actions-box="true">
                                        @foreach($cravings as $craving)
                                            <option data-tokens="{{$craving->name_en}}" value="{{$craving->id}}" >{{ucwords($craving->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Services</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" title="Choose one of the following..." id="service_ids" data-live-search="true" name="service_ids[]" multiple data-actions-box="true">
                                        @foreach($services as $service)
                                            <option data-tokens="{{$service->name_en}}" value="{{$service->id}}" >{{ucwords($service->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Table Persons List</label>
                                <select class="form-control" id="table_persons" name="table_persons[]" multiple="multiple">
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
                            <div class="form-group col-sm-3">
                                <label>Cost for one</label>
                                <input type="number" name="cost" step="any" id="cost" required class="form-control" placeholder="Cost" value="{{ old('cost') }}">
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-sm-4">
                                <label>Share Link</label>
                                <input type="url" name="share_link" id="share_link" class="form-control" placeholder="Share Link" value="{{ old('share_link') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Country Code</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="country_code" data-live-search="true" name="country_code" required>
                                        @foreach($countries as $country)
                                            <option data-content='<img src="{{$country->flag}}" width="17px" /> {{$country->name}} (+{{$country->calling_code}})' data-tokens="{{$country->name}}" value="+{{$country->calling_code}}" >{{$country->name}} (+{{$country->calling_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Mobile Number</label>
                                <input type="text" name="phone_number" required class="form-control" placeholder="Mobile Number" value="{{ old('phone_number') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Address Details</label>
                                <textarea name="address" id="address" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="Latitude" value="{{ old('latitude') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="Longitude" value="{{ old('longitude') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description English</label>
                                <textarea name="description_en" id="description_en" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="description_ar" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Main Image</h3>
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
                            <div class="form-group col-sm-4">
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">PDF</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="pdf" id="pdf">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Upload Images</label>
                                <div id="my-awesome-dropzone" class="dropzone" name="file[]">

                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.restaurants.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="loading style-2">
        <div class="loading-wheel"></div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
            allowedFormats: [ "pdf",'jpg', 'jpeg', 'png' ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
        $('#category_id').on('change', function () {
            if (this.value != '') {
                param = '/' + this.value ;
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: location.href.split("restaurants")[0]+ 'get_collections' + param,
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
    </script>
    <script>
        CKEDITOR.replace( 'description_en' );
        CKEDITOR.replace( 'description_ar' );
        $("#table_persons").select2({tags: true});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.loading').hide();
        Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false;
        var uploadUrl = location.href.split("/create")[0];
        var myDropzone = new Dropzone("#my-awesome-dropzone", {
            url: uploadUrl,
            method: 'post',
            addRemoveLinks: true,
            uploadMultiple: true,
            maxFiles: 10,
            maxFilesize: 10, // MB
            parallelUploads: 10000,
            acceptedFiles : 'image/*',
            autoProcessQueue: false,
            sendingmultiple: function(file, xhr, formData) {
            }
        });
        $('#submit').click(function (e) {
            $('.loading').show();
            e.preventDefault();
            var form = $("#submit_form");
            var form0 = form[0]; // You need to use standard javascript object here
            var formData = new FormData(form0);
            formData.append("description_en", CKEDITOR.instances["description_en"].getData());
            formData.append("description_ar", CKEDITOR.instances["description_ar"].getData());

            myDropzone.getAcceptedFiles().forEach(file => {
                formData.append("file[]", file);
            });
            // console.log(formData);
            if(myDropzone.getQueuedFiles().length > 0){
                myDropzone.processQueue();
            }else{
                myDropzone.uploadFiles([]);
            }
            var length = myDropzone.getQueuedFiles().length;
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: formData,
                contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                processData: false, // NEEDED, DON'T OMIT THIS
                success: function (data) {
                    // console.log(data);
                    location.assign(uploadUrl);
                },
                error: function (data) {
                    $(".alert-danger").show();
                    $.each(data.responseJSON, function (key, item)
                    {
                        $("#errors").text(item);
                    });
                    $('.loading').hide();

                }
            });

        });

    </script>
@endsection
