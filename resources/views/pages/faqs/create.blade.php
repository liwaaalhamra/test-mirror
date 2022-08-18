@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/faq') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Question</label>
                                <textarea name="question_en" id="desc1" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Answer</label>
                                <textarea name="answer_en" id="desc2" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Question Arabic</label>
                                <textarea name="question_ar" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Answer Arabic</label>
                                <textarea name="answer_ar" class="form-control"></textarea>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.faq.index') }}" class="btn btn-sm btn-dark">Back</a>
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
        CKEDITOR.replace( 'desc1' );
        CKEDITOR.replace( 'desc2' );
    </script>
@endsection
