@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/surveys/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="form-group col-sm-6">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ $item->name }}">
                        </div>
                        {{--<div class="form-group col-sm-6">--}}
                            {{--<label>Intro Space</label>--}}
                            {{--<input type="text" name="intro_space" id="intro_space" class="form-control" placeholder="Intro Space" value="{{ $item->intro_space }}">--}}
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
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            var start_datetime = "{{$item->start_datetime}}";
            if(start_datetime == ''){
                $('#datetimepicker1').datetimepicker();
            }else{
                var start_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->start_datetime)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker1').datetimepicker({
                    defaultDate: start_datetime_formatted
                });
            }

            var end_datetime = "{{$item->end_datetime}}";
            if(end_datetime == ''){
                $('#datetimepicker2').datetimepicker();
            }else{
                var end_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->end_datetime)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker2').datetimepicker({
                    defaultDate: end_datetime_formatted
                });
            }
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
