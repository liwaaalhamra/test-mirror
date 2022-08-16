@extends('vendor.layout')

@section('content')
    <div class="row">
        <form id="submit_form" action="{{ secure_url('admin/restaurants_cafes/'.$category->id.'/items/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Update Item of {{ucwords($category->name)}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $item->name }}">
                                <label id="error1" style="color: red;visibility: hidden">This field is required!</label>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Cuisine Type</label>
                                <input type="text" name="cuisine_type" id="cuisine_type" class="form-control" placeholder="Cuisine Type" value="{{ $item->cuisine_type }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control">{{$item->description}}</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Level</label>
                                <input type="text" name="level" id="level" class="form-control" placeholder="Level" value="{{ $item->level }}">
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
                            <a href="{{ route('admin.restaurants_cafes.items.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
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
        var item_id = "{{$item->id}}";
        var category_id = "{{$item->category_id}}";
        Dropzone.options.myAwesomeDropzone = false;
        Dropzone.autoDiscover = false;
        var uploadUrl = location.href.split("restaurants_cafes")[0] + 'restaurants_cafes/items/update_item';
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
                formData.append("name", $('#name').val());
                formData.append("cuisine_type", $('#cuisine_type').val());
                formData.append("level", $('#level').val());
                formData.append("_token", $('#_token').val());
                formData.append("item_id", item_id);
                formData.append("_token", $('#_token').val());
            }
        });

        var uploadedUrl = location.href.split("restaurants_cafes")[0]+'restaurants_cafes/items/get_uploaded_images';
        var imageUrl = location.href.split("admin")[0]+'public/images/restaurants_items/';
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
            var deleteUrl = location.href.split("restaurants_cafes")[0] + 'restaurants_cafes/items/delete_images';
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
            if($('#title').val() == ""){
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
            var redirectUrl = location.href.split("restaurants_cafes")[0] + 'restaurants_cafes/'+category_id+'/items';
            setTimeout(function() {
                location.assign(redirectUrl);
            }, time);
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
           toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
           content_css: '//www.tinymce.com/css/codepen.min.css'
       });
    </script>
@endsection
