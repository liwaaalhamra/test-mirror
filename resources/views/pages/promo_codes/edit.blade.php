@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/promo_codes/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Code</label>
                                <input type="text" name="code" id="code" class="form-control" required placeholder="Code" value="{{ $item->code }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Max uses</label>
                                <input type="number" step="1" min="1" name="max_uses" id="max_uses" class="form-control" placeholder="Max uses" value="{{ $item->max_uses }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Amount Value</label>
                                <input type="number" step="any" min="1" name="amount" id="amount" class="form-control" placeholder="Amount Value" value="{{ $item->amount }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Choose Amount Type</label>
                                <select class="form-control selectpicker" title="Choose..." required  data-live-search="true" id="type" name="type">
                                    @foreach($types as $key => $value)
                                        <option data-tokens="{{$key}}" @if($item->type == $key) selected @endif value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>DateTime From</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="start_datetime" id="start_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>DateTime To</label>
                                <div class='input-group date' id='datetimepicker2' style="margin-bottom: 10px">
                                    <input type='text' name="end_datetime" id="end_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.promo_codes.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            var start_datetime = "{{$item->start_date}}";
            if(start_datetime == ''){
                $('#datetimepicker1').datetimepicker();
            }else{
                var start_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->start_date)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker1').datetimepicker({
                    defaultDate: start_datetime_formatted
                });
            }

            var end_datetime = "{{$item->end_date}}";
            if(end_datetime == ''){
                $('#datetimepicker2').datetimepicker();
            }else{
                var end_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->end_date)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker2').datetimepicker({
                    defaultDate: end_datetime_formatted
                });
            }
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
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
