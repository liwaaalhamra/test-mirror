@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                    <form action="{{ secure_url('admin/about_us') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description English</label>
                                <textarea name="description_en" rows="10" id="desc_en" class="form-control">@if(!empty($about_us)) {{ $about_us->description_en }} @endif</textarea>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Description Arabic</label>
                                <textarea name="description_ar" rows="10" id="desc_ar" class="form-control">@if(!empty($about_us)) {{ $about_us->description_ar }} @endif</textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>About Us Image</label>
                                <!-- bootstrap-imageupload. -->
                                <div class="imageupload panel panel-default">
                                    <div class="panel-heading clearfix">
                                        <h3 class="panel-title pull-left">Upload Image</h3>
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>

                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <!-- The file is stored here. -->
                                            <input type="file" name="image-file">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                            <label>Share Message</label>
                            <input type="text" name="message" class="form-control" placeholder="Message" @if(!empty($about_us))value="{{ $about_us->message }}"@endif>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Facebook</label>
                                <input type="text" name="facebook" class="form-control" placeholder="Facebook" @if(!empty($about_us))value="{{ $about_us->facebook }}"@endif>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Twitter</label>
                                <input type="text" name="twitter" class="form-control" placeholder="Twitter" @if(!empty($about_us))value="{{ $about_us->twitter }}"@endif>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Website</label>
                                <input type="text" name="website" class="form-control" placeholder="Website" @if(!empty($about_us))value="{{ $about_us->website }}"@endif>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Instagram</label>
                                <input type="text" name="instagram" class="form-control" placeholder="Instagram" @if(!empty($about_us))value="{{ $about_us->instagram }}"@endif>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" @if(!empty($about_us))value="{{ $about_us->email }}"@endif>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                @if(!empty($about_us) && $about_us->image != null)
                                    <a href="{{secure_url('admin/about_us/delete')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing Image</a>
                                @endif
                            </div>
                            <div class="form-group col-sm-8">

                                @if(!empty($about_us) && $about_us->image != null)
                                    <img  alt="Image Preview"  src="{{ secure_url("uploads/images/about_us/".$about_us->image) }}" class="thumbnail" data-toggle="modal" data-target="#myModal"  style="cursor: pointer;max-width:400px;max-height: 400px"/>
                                    <div id="myModal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ secure_url("uploads/images/about_us/".$about_us->image) }}" width="100%;">
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

                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary">Save</button>
                        </div>
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

        //    tinymce.init({
        //        selector: 'textarea',
        //        height: 500,
        //        menubar: true,
        //        plugins: [
        //            'advlist autolink lists link image charmap print preview anchor',
        //            'searchreplace visualblocks code fullscreen',
        //            'insertdatetime media table contextmenu paste code'
        //        ],
        //        toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        //        content_css: '//www.tinymce.com/css/codepen.min.css'
        //    });
        // tinymce.init({
        //     selector: 'textarea',
        //     height: 200,
        //     menubar: false,
        //     plugins: [
        //     ],
        //     toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
        //     content_css: '//www.tinymce.com/css/codepen.min.css'
        // });
        CKEDITOR.replace( 'desc_en' );
        CKEDITOR.replace( 'desc_ar' );
    </script>
@endsection