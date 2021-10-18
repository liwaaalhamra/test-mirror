@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/memos/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Caption</label>
                                <input type="text" name="caption" id="caption" class="form-control" required placeholder="Caption" value="{{ $item->caption }}">
                            </div>
                            {{--<div class="form-group col-sm-6">--}}
                                {{--<label>Link</label>--}}
                                {{--<input type="text" name="link" id="link" class="form-control" placeholder="Link" value="{{ $item->link }}">--}}
                            {{--</div>--}}

                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Description</label>
                                <textarea rows="3" name="description" class="form-control" placeholder="Description">{{ $item->description }}</textarea>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>DateTime</label>
                                <div class='input-group date' id='datetimepicker1' style="margin-bottom: 10px">
                                    <input type='text' name="item_datetime" id="item_datetime" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <div class="imageupload upload_pdf panel panel-default">
                                    <div class="panel-heading clearfix" style="text-align: center">
                                        <h3 class="panel-title pull-left">PDF</h3>

                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn btn-default active">File</button>
                                        </div>
                                    </div>
                                    <div class="file-tab panel-body">
                                        <label class="btn btn-default btn-file">
                                            <span>Browse</span>
                                            <input type="file" name="pdf" id="pdf">
                                        </label>
                                        <button type="button" class="btn btn-default">Remove</button>
                                        @if(!empty($item) && $item->getPdfName() != null)
                                            <a href="{{secure_url('admin/memos/'.$item->id.'/pdf/delete_single')}}" class="btn btn-sm btn-danger pull-right delete">Delete Existing PDF</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                @if(!empty($item) && $item->getPdfName() != null)
                                    <object type="application/pdf" data="{{ $item->pdf }}" width="100%" height="500">No Support</object>
                                    <br><br>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.memos.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            var item_datetime = "{{$item->memo_datetime}}";
            if(item_datetime == ''){
                $('#datetimepicker1').datetimepicker();
            }else{
                var item_datetime_formatted = "{{\Carbon\Carbon::createFromTimestamp($item->memo_datetime)->format('Y-m-d H:i:s')}}";
                $('#datetimepicker1').datetimepicker({
                    defaultDate: item_datetime_formatted
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
        $imageupload.imageupload({
            allowedFormats: [ "pdf","png","jpeg","jpg" ],
//            previewWidth: 250,
//            previewHeight: 250,
            maxFileSizeKb: 10240
        });
    </script>
@endsection
