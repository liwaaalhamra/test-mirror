@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Working Hours of {{ucwords($restaurant->name_en)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.restaurants.timing.store',$restaurant->id) }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Type</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="type" required>
                                    @foreach($types as $type)
                                        <option data-tokens="{{$type}}" value="{{ $type }}"> {{ucwords($type)}} Hours</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Choose Day</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="day" required>
                                    @foreach($days as $day_id => $day)
                                        <option data-tokens="{{$day}}" value="{{ $day_id }}"> {{$day}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Time From</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="start_datetime" id="start_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Time To</label>
                                <div class='input-group date' id='datetimepicker2' style="margin-bottom: 10px">
                                    <input type='text' name="end_datetime" id="end_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                                </span>
                                </div>
                            </div>
                        </div>


                        <button type="submit" id="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.restaurants.timing.index',$restaurant->id) }}" class="btn btn-sm btn-dark">Back</a>
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
            $('#datetimepicker1').datetimepicker({
                format: 'LT'
            });
            $('#datetimepicker2').datetimepicker({
                format: 'LT'
            });
            $("#datetimepicker1").on("dp.change", function (e) {
                $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker2").on("dp.change", function (e) {
                $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
            });
        });
    </script>
@endsection
