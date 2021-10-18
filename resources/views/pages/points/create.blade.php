@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/points') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Users</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" multiple name="user_ids[]" required data-actions-box="true">
                                    @foreach($types as $type)
                                        <optgroup label="{{$type->cost_center}}">
                                            @foreach($users as $item)
                                                @if($item->cost_center == $type->cost_center)
                                                    <option data-tokens="{{$item->first_name}},{{$item->last_name}},{{$item->cost_center}},{{$item->cost_center}} {{$item->first_name}} {{$item->last_name}},{{$item->cost_center}} {{$item->last_name}},{{$item->first_name}} {{$item->cost_center}},{{$item->last_name}} {{$item->cost_center}}" value="{{ $item->id }}">{{$item->first_name}} {{$item->last_name}}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Points</label>
                                <input type="number" min="{{$min}}" max="{{$max}}" name="points" id="points" class="form-control" placeholder="Points" value="{{ old('points') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Description</label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Description" value="{{ old('description') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Date</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="item_datetime" id="item_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.points.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            // $('#datetimepicker1').datetimepicker();
            $('#datetimepicker1').datetimepicker({
                format: 'L'
            });
        });
        var $imageupload = $('.imageupload');
        $imageupload.imageupload({
//            allowedFormats: [ "pdf" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
        // tinymce.init({
        //     selector: 'textarea',
        //     height: 200,
        //     menubar: false,
        //     plugins: [
        //         'advlist autolink lists link image charmap print preview anchor',
        //     ],
        //     toolbar: 'undo redo  | styleselect | bold italic | bullist numlist outdent indent | link',
        //     content_css: '//www.tinymce.com/css/codepen.min.css'
        // });
    </script>
@endsection
