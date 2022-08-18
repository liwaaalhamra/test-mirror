@extends('vendor.layout')

@section('content')
    {{--<div class="row">--}}
    {{--<div class="panel panel-default" style="text-align: center">--}}
    {{--<div class="ibox-title">--}}
    {{--<h3><span class="label label-success pull-right"><i class="fab fa-searchengin"></i></span></h3>--}}
    {{--</div>--}}
    {{--<div class="panel-body">--}}
    {{--<div class=" font-bold ">--}}
    {{--<form action="{{ secure_url('admin/dashboard') }}" method="get" role="search" id="filter_form" >--}}
    {{--<div class="col-sm-3 m-b-xs">--}}
    {{--<label><strong>Filter by User Type</strong></label>--}}
    {{--<select onchange="this.form.submit()" class="form-control selectpicker" id="type" data-live-search="true" name="type" >--}}
    {{--<option data-tokens="All" @if($selected_type == 'all_users') selected @endif value="all_users" >All Users</option>--}}
    {{--<option data-tokens="App" @if($selected_type == 'app_users') selected @endif value="app_users" >App Users</option>--}}
    {{--<option data-tokens="Card" @if($selected_type == 'card_users') selected @endif value="card_users" >Card Users</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--<div class="col-sm-3 m-b-xs">--}}
    {{--<label>Date From</label>--}}
    {{--<div class='input-group date' id='from'>--}}
    {{--<input type='text' name="min" id="min" required class="form-control" />--}}
    {{--<span class="input-group-addon">--}}
    {{--<span class="glyphicon glyphicon-calendar"></span>--}}
    {{--</span>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-sm-3 m-b-xs">--}}
    {{--<label>Date To</label>--}}
    {{--<div class='input-group date' id='to'>--}}
    {{--<input type='text' name="max" id="max" required class="form-control" />--}}
    {{--<span class="input-group-addon">--}}
    {{--<span class="glyphicon glyphicon-calendar"></span>--}}
    {{--</span>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-sm-2 m-b-xs">--}}
    {{--<button type="submit" class="btn btn-lg btn-action">GO</button>--}}
    {{--</div>--}}
    {{--</form>--}}

    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    <div class="row">
        @foreach($stats as $single_stat)
            <div class="col-lg-{{$single_stat->meta_col}} col-md-{{$single_stat->meta_col}} col-sm-{{$single_stat->meta_col}} col-xs-12">
                <a href="{{secure_url($single_stat->meta_route)}}">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h3><span class="label label-success pull-right"><i class="{{$single_stat->meta_icon}}"></i></span></h3>
                            <h3>{{$single_stat->meta_name}}</h3>
                        </div>
                        <div class="ibox-content" style="min-height: 133px">
                            <div class=" font-bold ">
                                <h2 class="no-margins counter-count">{{$single_stat->meta_value}}  <small><i class=" text-success">{{$single_stat->meta_unit}}</i></small></h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

@endsection
@section('scripts')
    @parent
    <script>

        {{--$(function () {--}}
        {{--var from_datetime = "{{$from}}";--}}
        {{--var to_datetime = "{{$to}}";--}}
        {{--if(!from_datetime.trim()){--}}
        {{--$('#from').datetimepicker({--}}
        {{--format: 'YYYY/MM/DD'--}}
        {{--});--}}
        {{--}else{--}}
        {{--var from_formatted = "{{\Carbon\Carbon::createFromTimestamp($from)->format('Y-m-d')}}";--}}
        {{--$('#from').datetimepicker({--}}
        {{--format: 'YYYY/MM/DD',--}}
        {{--defaultDate: from_formatted--}}
        {{--});--}}
        {{--}--}}
        {{--if(!to_datetime.trim()){--}}
        {{--$('#to').datetimepicker({--}}
        {{--format: 'YYYY/MM/DD'--}}
        {{--});--}}
        {{--}else{--}}
        {{--var from_formatted = "{{\Carbon\Carbon::createFromTimestamp($to)->format('Y-m-d')}}";--}}
        {{--$('#to').datetimepicker({--}}
        {{--format: 'YYYY/MM/DD',--}}
        {{--defaultDate: from_formatted--}}
        {{--});--}}
        {{--}--}}
        {{--});--}}
    </script>
@endsection
