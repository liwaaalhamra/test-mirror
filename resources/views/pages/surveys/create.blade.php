@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/surveys') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group col-sm-6">
                            <label>Name</label>
                            <input type="text" name="name" id="name" required class="form-control" placeholder="Name" value="{{ old('name') }}">
                        </div>
                        {{--<div class="form-group col-sm-6">--}}
                            {{--<label>Intro Space</label>--}}
                            {{--<input type="text" name="intro_space" id="intro_space" class="form-control" placeholder="Intro Space" value="{{ old('intro_space') }}">--}}
                        {{--</div>--}}
                        <div class="form-group col-sm-3">
                            <label>Start DateTime</label>
                            <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                <input type='text' name="start_datetime" id="start_datetime" class="form-control" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <label>End DateTime</label>
                            <div class='input-group date' id='datetimepicker2' style="margin-bottom: 10px">
                                <input type='text' name="end_datetime" id="end_datetime" class="form-control" />
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            $('#datetimepicker2').datetimepicker();
            $("#datetimepicker1").on("dp.change", function (e) {
                $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker2").on("dp.change", function (e) {
                $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
            });
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
        tinymce.init({
            selector: 'textarea',
            height: 200,
            menubar: false,
            plugins: [
            ],
            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    </script>
@endsection
