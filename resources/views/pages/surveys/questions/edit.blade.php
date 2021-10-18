@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/surveys/'.$category->id.'/questions/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Question</label>
                                <input type="text" name="question" id="question" class="form-control" required placeholder="Question" value="{{ $item->question }}">
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-6">--}}
                                {{--<label>Type</label>--}}
                                {{--<select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="type" required>--}}
                                    {{--<option data-tokens="single" @if($item->type == 'single') selected @endif value="single">Single Choice</option>--}}
                                    {{--<option data-tokens="multiple" @if($item->type == 'multiple') selected @endif value="multiple">Multiple Choice</option>--}}
                                    {{--<option data-tokens="free text" @if($item->type == 'free_text') selected @endif value="free_text">Free Text</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.surveys.questions.index',$category->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
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
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
