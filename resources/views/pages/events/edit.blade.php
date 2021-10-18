@extends('vendor.layout')

@section('content')
    <div class="row">
        <form id="submit_form" action="{{ secure_url('admin/events/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">

                            <div class="form-group col-sm-6">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $item->name }}">
                                <label id="error1" style="color: red;visibility: hidden">This field is required!</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Link</label>
                                <input type="text" name="link" id="link" class="form-control" placeholder="Link" value="{{ $item->link }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="panel-heading font-bold">Set Location To Enable Checkin Feature</div>
                            <div class="form-group col-sm-4">
                                <label>Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="Latitude" value="{{ $item->latitude }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="Longitude" value="{{ $item->longitude }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Checkin Points</label>
                                <input type="number" step="any" min="0" name="points" id="points" class="form-control" placeholder="Points" value="{{ $item->points }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Description</label>
                                <textarea name="description" id="description"  rows="10" class="form-control description">{{$item->description}}</textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Short Description</label>
                                <textarea name="short_description" id="short_description"  rows="10" class="form-control">{{$item->short_description}}</textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>DateTime</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="item_datetime" id="item_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
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
                        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            var item_datetime = "{{$item->item_datetime}}";
            if(item_datetime == ''){
                $('#datetimepicker1').datetimepicker();
            }else{
                var item_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->item_datetime)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker1').datetimepicker({
                    defaultDate: item_datetime_formatted
                });
            }
        });
        tinymce.init({
            selector: 'textarea.description',
            height: 200,
            menubar: false,
            plugins: [
            ],
            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
        var item_id = "{{$item->id}}";
        Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false;
        var uploadUrl = location.href.split("events")[0] + 'events/update_item';
        var myDropzone = new Dropzone("#my-awesome-dropzone", {
            url: uploadUrl,
            method: 'post',
            //headers : {'post_text': post_text},
            addRemoveLinks: true,
            uploadMultiple: true,
            maxFiles: 10,
            maxFilesize: 10, // MB
            parallelUploads: 10000,
            acceptedFiles : 'image/*',
            autoProcessQueue: false,
            sendingmultiple: function(file, xhr, formData) {
                formData.append("description", tinyMCE.editors[$('#description').attr('id')].getContent());
                // formData.append("description", $('#description').val());
                formData.append("short_description", $('#short_description').val());
                formData.append("latitude", $('#latitude').val());
                formData.append("longitude", $('#longitude').val());
                formData.append("points", $('#points').val());
                formData.append("item_datetime", $('#item_datetime').val());
                formData.append("name", $('#name').val());
                formData.append("link", $('#link').val());
                formData.append("item_id", item_id);
                formData.append("_token", $('#_token').val());
            }
        });

        var uploadedUrl = location.href.split("events")[0]+'events/get_uploaded_images';
        var imageUrl = location.href.split("admin")[0]+'public/uploads/images/events/';
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
            var deleteUrl = location.href.split("events")[0] + 'events/delete_images';
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
        $('#submit').click(function(e){
            e.preventDefault();
            if($('#name').val() == ""){
                $('#error1').css('visibility','visible');
                return;
            }else{
                $('#error1').css('visibility','hidden');
            }
            //e.stopPropagation();
            if(myDropzone.getQueuedFiles().length > 0){
                myDropzone.processQueue();
            }else{
                myDropzone.uploadFiles([]);
            }
            $( "#submit_form" ).submit();
            var length = myDropzone.getQueuedFiles().length;
            var time = 2000;
            if(length > 0){
                time = 1000 * length;
            }
            var redirectUrl = location.href.split("events")[0] + 'events';
            setTimeout(function() {
                location.assign(redirectUrl);
            }, time);
        });
    </script>
@endsection
