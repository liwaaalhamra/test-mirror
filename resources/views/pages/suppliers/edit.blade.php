@extends('vendor.layout')

@section('content')
    <div class="alert alert-danger" style="display: none">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <p id="errors"></p>
    </div>
    <div class="row">
        <form id="submit_form" action="{{ route('admin.suppliers.update',$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Username</label>
                                <input type="text" id="username" name="username" value="{{$item->username}}" class="form-control" >
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Order</label>
                                <input type="text" name="item_order" id="item_order" required class="form-control" placeholder="Order" value="{{ $item->item_order }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Is Closed ?</label>
                                <input name="is_closed" type="checkbox" @if($item->is_closed) checked @endif data-toggle="toggle" data-on="YES"  data-off="NO" data-onstyle="success" data-size="large">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="control-label" >Choose Category</label>
                                <div class="form-group">
                                    <select class="form-control selectpicker" required title="Choose one of the following..." id="category_id" data-live-search="true" name="category_id" data-actions-box="true">
                                        @foreach($categories as $category)
                                            <option data-tokens="{{$category->name_en}}" @if($item->category_id == $category->id) selected @endif value="{{$category->id}}" >{{ucwords($category->name_en)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="Name English" value="{{ $item->name_en }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" required placeholder="Name Arabic" value="{{ $item->name_ar }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Share Link</label>
                                <input type="url" name="share_link" id="share_link" class="form-control" placeholder="Share Link" value="{{ $item->share_link }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Country Code</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="country_code" data-live-search="true" name="country_code" required>
                                        @foreach($countries as $country)
                                            <option data-content='<img src="{{$country->flag}}" width="17px" /> {{$country->name}} (+{{$country->calling_code}})' @if($item->country_code == $country->calling_code) selected @endif data-tokens="{{$country->name}}" value="+{{$country->calling_code}}" >{{$country->name}} (+{{$country->calling_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Mobile Number</label>
                                <input type="text" name="phone_number" required class="form-control" placeholder="Mobile Number" value="{{ $item->phone_number }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Address Details</label>
                                <textarea name="address" id="address" class="form-control">{{$item->address}}</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="Latitude" value="{{ $item->latitude }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="Longitude" value="{{ $item->longitude }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description English</label>
                                <textarea name="description_en" id="description_en" class="form-control">{{$item->description_en}}</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" id="description_ar" class="form-control">{{$item->description_ar}}</textarea>
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
                                            <a href="{{secure_url('admin/suppliers/'.$item->id.'/image/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Main Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->getMediaName() != null)
                                                <img  alt="Image Preview"  src="{{ $item->image }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                <div id="logo" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h3 class="modal-title">Main Image</h3>
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
                            <div class="form-group col-sm-4">
                                <div class="imageupload upload_pdf panel panel-default">
                                    <div class="panel-heading clearfix" style="text-align: center">
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
                                        @if(!empty($item) && $item->getPdfName() != null)
                                            <a href="{{secure_url('admin/suppliers/'.$item->id.'/pdf/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing PDF</a>
                                        @endif
                                    </div>
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">Main Image</h3>
                                        <div class="btn-group">
                                            @if(!empty($item) && $item->getPdfName() != null)
                                                <object type="application/pdf" data="{{ $item->pdf }}" width="100%" height="500">No Support</object>
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
                            <div class="form-group col-sm-12">
                                <label>Upload Images</label>
                                <div id="my-awesome-dropzone" class="dropzone" name="file[]">

                                </div>
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-dark">Back</a>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="loading style-2">
        <div class="loading-wheel"></div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            var item_datetime = "{{$item->expiry_date}}";
            if(item_datetime == ''){
                $('#datetimepicker1').datetimepicker();
            }else{
                var item_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->expiry_date)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker1').datetimepicker({
                    defaultDate: item_datetime_formatted
                });
            }
        });
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
            allowedFormats: [ "pdf",'jpg', 'jpeg', 'png' ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
    </script>
    <script>
        $("#table_persons").select2({tags: true});
        CKEDITOR.replace( 'description_en' );
        CKEDITOR.replace( 'description_ar' );
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.loading').hide();
        var item_id = "{{$item->id}}";
        Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false;
        var uploadUrl = location.href.split("suppliers")[0] + 'suppliers/'+item_id;
        var myDropzone = new Dropzone("#my-awesome-dropzone", {
            url: uploadUrl,
            method: 'put',
            //headers : {'post_text': post_text},
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

        var uploadedUrl = location.href.split("suppliers")[0]+'suppliers/get_uploaded_images';
        var imageUrl = location.href.split("admin")[0]+'uploads/images/suppliers/';
        /*
         get existing images
         */
        var http = new XMLHttpRequest();
        http.open("POST", uploadedUrl, true);
        //Send the proper header information along with the request
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        var params = "item_id="+item_id+"&_token="+"{{ csrf_token() }}";

        http.onreadystatechange = function() {//Call a function when the state changes.
            if(http.readyState == 4 && http.status == 200) {
                var images = JSON.parse(http.responseText);

                $.each(images, function(key,value){
                    var file_name = '';
                    var file_size = '';
                    var i = 0;
                    $.each(value, function(key,value){
                        i = i +1 ;
                        if(key == 'name'){
                            file_name = value;
                        }else{
                            file_size = value;
                        }
                        if(i%2 == 0) {
                            var mockFile = {name: file_name, size: file_size};
                            myDropzone.options.addedfile.call(myDropzone, mockFile);
                            myDropzone.options.thumbnail.call(myDropzone, mockFile, imageUrl+file_name);
                            myDropzone.emit("success", mockFile);
                            $(".dz-progress").remove();
                        }
                    });


                });
            }
        }
        http.send(params);

        /*
         delete images
         */
        myDropzone.on("removedfile", function(file) {
            console.log('deleted file info: '+file);
            var deleteUrl = location.href.split("suppliers")[0] + 'suppliers/delete_images';
            $.ajax({
                url: deleteUrl,
                type: "POST",
                data: {
                    file_name : file.name ,
                    id : item_id,
                    _token:"{{ csrf_token() }}"
                }
            });
        });
        /*
         submit form
         */
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
            if(myDropzone.getQueuedFiles().length > 0){
                myDropzone.processQueue();
            }else{
                myDropzone.uploadFiles([]);
            }
            var length = myDropzone.getQueuedFiles().length;
            var redirectUrl = location.href.split("suppliers")[0] + 'suppliers';
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: formData,
                contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
                processData: false, // NEEDED, DON'T OMIT THIS
                success: function (data) {
                    // console.log(data);
                    // alert('test');
                    location.assign(redirectUrl);
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
