@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/promo_codes') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Code</label>
                                <input type="text" name="code" id="code" class="form-control" required placeholder="Code" value="{{ old('code') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Max uses</label>
                                <input type="number" step="1" min="1" name="max_uses" id="max_uses" class="form-control" placeholder="Max uses" value="{{ old('max_uses') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Amount Value</label>
                                <input type="number" step="any" min="1" name="amount" id="amount" class="form-control" placeholder="Amount Value" value="{{ old('amount') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Choose Amount Type</label>
                                <select class="form-control selectpicker" title="Choose..." required  data-live-search="true" id="type" name="type">
                                    @foreach($types as $key => $item)
                                        <option data-tokens="{{$key}}" value="{{ $key }}">{{ $item }}</option>
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

                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.promo_codes.index') }}" class="btn btn-sm btn-dark">Back</a>
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
        });
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
