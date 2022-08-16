@extends('vendor.layout')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Items of {{ucwords($category->name)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ secure_url('admin/restaurants_cafes'.$category->id.'/items') }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ old('name') }}">
                                <label id="error1" style="color: red;visibility: hidden">This field is required!</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Cuisine Type</label>
                                <input type="text" name="cuisine_type" id="cuisine_type" class="form-control" placeholder="Cuisine Type" value="{{ old('cuisine_type') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Level</label>
                                <input type="text" name="level" id="level" class="form-control" placeholder="Level" value="{{ old('level') }}">
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
                        <a href="{{ route('admin.restaurants_cafes.items.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>

        Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false;
        var uploadUrl = location.href.split("/create")[0];
        //var post_text = document.getElementById('post_text').value;
        var myDropzone = new Dropzone("#my-awesome-dropzone", {
            url: uploadUrl,
            method: 'post',
            addRemoveLinks: true,
            uploadMultiple: true,
            maxFiles: 10,
            maxFilesize: 2, // MB
            parallelUploads: 10000,
            acceptedFiles : 'image/*',
            autoProcessQueue: false,
            sendingmultiple: function(file, xhr, formData) {
               formData.append("description", tinyMCE.editors[$('#description').attr('id')].getContent());
                // formData.append("description", $('#description').val());
                formData.append("name", $('#name').val());
                formData.append("cuisine_type", $('#cuisine_type').val());
                formData.append("level", $('#level').val());
                formData.append("_token", $('#_token').val());
            }
        });
        $('#submit').click(function(e){
            e.preventDefault();

            if($('#title').val() == ""){
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


//            $.get(uploadUrl, function(data, status){
//
//            });
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
