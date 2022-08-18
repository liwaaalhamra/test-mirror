@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ secure_url('admin/events') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
                                <label id="error1" style="color: red;visibility: hidden">This field is required!</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Link</label>
                                <input type="text" name="link" id="link" class="form-control" placeholder="Link" value="{{ old('link') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="panel-heading font-bold">Set Location To Enable Checkin Feature</div>
                            <div class="form-group col-sm-4">
                                <label>Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="Latitude" value="{{ old('latitude') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="Longitude" value="{{ old('longitude') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Checkin Points</label>
                                <input type="number" step="any" min="0" name="points" id="points" class="form-control" placeholder="Points" value="{{ old('points') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Description</label>
                                <textarea name="description" id="description" rows="10" class="form-control description"></textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Short Description</label>
                                <textarea name="short_description" id="short_description" rows="10" class="form-control "></textarea>
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

                        <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function () {
            $('#datetimepicker1').datetimepicker();
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
               formData.append("description", tinyMCE.editors[$('#description').attr('id')].getContent());
                // formData.append("description", $('#description').val());
                formData.append("short_description", $('#short_description').val());
                formData.append("latitude", $('#latitude').val());
                formData.append("longitude", $('#longitude').val());
                formData.append("points", $('#points').val());
                formData.append("item_datetime", $('#item_datetime').val());
                formData.append("name", $('#name').val());
                formData.append("link", $('#link').val());
                formData.append("_token", $('#_token').val());
            }
        });
        $('#submit').click(function(e){
            e.preventDefault();

            if($('#name').val() == ""){
                $('#error1').css('visibility','visible');
                return;
            }else{
                $('#error1').css('visibility','hidden');
            }
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
            setTimeout(function() {
                location.assign(uploadUrl);
            }, time);
        });
        $(function () {
            var $imageupload = $('.imageupload');
            $imageupload.imageupload({
//                allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
                maxFileSizeKb: 10240
            });
        });
    </script>
@endsection
