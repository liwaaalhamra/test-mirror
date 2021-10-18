@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/surveys/'.$category->id.'/questions') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Question</label>
                                <input type="text" name="question" id="question" class="form-control" required placeholder="Name" value="{{ old('question') }}">
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-6">--}}
                                {{--<label>Type</label>--}}
                                {{--<select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="type" required>--}}
                                    {{--<option data-tokens="single" value="single">Single Choice</option>--}}
                                    {{--<option data-tokens="multiple" value="multiple">Multiple Choice</option>--}}
                                    {{--<option data-tokens="free text" value="free_text">Free Text</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.surveys.questions.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
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
