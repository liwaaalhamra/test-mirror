@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Endpoints</div>
                <div class="panel-body">
                    <ul>
                        @php $i=0 @endphp
                    @foreach($endpoints as $key => $endpoint)
                            @php $i=$i+1; @endphp
                        <div class="col-sm-4">
                            <li>
                                <strong>{{$key}}</strong> => <label id="p{{$i}}">{{$endpoint}}</label> <button onclick="copyToClipboard('#p{{$i}}')">Copy</button>
                            </li>
                        </div>
                        <br>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Field Names</div>
                <div class="panel-body">
                    <ul>
                        @php $i=0 @endphp
                    @foreach($field_names as $key => $field_name)
                            @php $i=$i+1; @endphp
                        <div class="col-sm-4">
                            <li>
                                <strong>{{$key}}</strong> => <label id="field_name_{{$i}}">{{$field_name}}</label> <button onclick="copyToClipboard('#field_name_{{$i}}')">Copy</button>
                            </li>
                        </div>
                        <br>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/dynamic_fields') }}" method="post">
                        {{ csrf_field() }}
{{--                        <div class="form-group">--}}
{{--                            <label>Category/Sub 1/Sub 2</label>--}}
{{--                            <select class="form-control selectpicker" required title="Choose one of the following..." id="parent_ids" multiple data-live-search="true" name="parent_ids[]" data-actions-box="true">--}}
{{--                                @foreach($sub_sub_categories as $sub_sub_category)--}}
{{--                                    <option data-tokens="{{$sub_sub_category->name_en}}{{$sub_sub_category->sub_category->name_en}}{{$sub_sub_category->sub_category->category->name_en}}" value="{{$sub_sub_category->sub_category->category_id}}_{{$sub_sub_category->sub_category_id}}/{{$sub_sub_category->id}}" >{{$sub_sub_category->sub_category->category->name_en}}/{{$sub_sub_category->sub_category->name_en}}/{{$sub_sub_category->name_en}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
                        <div class="form-group">
                            <label>Category/Sub 1</label>
                            <select class="form-control selectpicker" required title="Choose one of the following..." id="parent_ids" multiple data-live-search="true" name="parent_ids[]" data-actions-box="true">
                                @foreach($sub_categories as $sub_category)
                                    <option data-tokens="{{$sub_category->name_en}}{{$sub_category->category->name_en}}" value="{{$sub_category->category_id}}_{{$sub_category->id}}" >{{$sub_category->category->name_en}}/{{$sub_category->name_en}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Field exists in Return ?</label>
                            <input name="in_return" id="select_row" type="checkbox" data-toggle="toggle" data-on="YES" data-off="NO" data-onstyle="success" data-offstyle="danger" data-size="large">
                        </div>
                        <div class="form-group">
                            <label>Field exists in Posting Filters ?</label>
                            <input name="in_filters" id="select_row" type="checkbox" data-toggle="toggle" data-on="YES" data-off="NO" data-onstyle="success" data-offstyle="danger" data-size="large">
                        </div>
                        <div class="form-group">
                            <label>Field exists in Browsing Filters ?</label>
                            <input name="in_browsing_filters" id="select_row" type="checkbox" data-toggle="toggle" data-on="YES" data-off="NO" data-onstyle="success" data-offstyle="danger" data-size="large">
                        </div>

                        <div class="form-group">
                            <label>Field Name (Column Name in Database, without spaces/capital letter)</label>
                            <input type="text" name="field_name" id="field_name" required class="form-control" placeholder="Field Name" value="{{ old('field_name') }}">
                        </div>
                        <div class="form-group">
                            <label>Field Title (Display in App)</label>
                            <input type="text" name="field_title" id="field_title" required class="form-control" placeholder="Field Title" value="{{ old('field_title') }}">
                        </div>
                        <div class="form-group">
                            <label>Field Type</label>
                            <select class="form-control selectpicker" required title="Choose one of the following..." id="field_type" data-live-search="true" name="field_type" data-actions-box="true">
                                @foreach($field_types as $field_type_value => $field_type)
                                    <option data-tokens="{{$field_type}}" value="{{$field_type_value}}" >{{$field_type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Field Endpoint (Needed in case of Field of Type "List", Refer to list on Top of page)</label>
                            <input type="text" name="field_endpoint" id="field_endpoint" class="form-control" placeholder="Field Endpoint" value="{{ old('field_endpoint') }}">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>has Extra Fields ?</label>
                            <input name="has_extra_field" id="has_extra_field" type="checkbox" data-toggle="toggle" data-on="YES" data-off="NO" data-onstyle="success" data-offstyle="danger" data-size="large">
                        </div>
                        <div class="form-group">
                            <label>Extra Field Name (Column Name in Database, without spaces/capital letter)</label>
                            <input type="text" name="extra_field_name" id="extra_field_name" class="form-control" placeholder="Extra Field Name" value="{{ old('extra_field_name') }}">
                        </div>
                        <div class="form-group">
                            <label>Extra Field Title (Display in App)</label>
                            <input type="text" name="extra_field_title" id="extra_field_title" class="form-control" placeholder="Extra Field Title" value="{{ old('extra_field_title') }}">
                        </div>
                        <div class="form-group">
                            <label>Extra Field Type</label>
                            <select class="form-control selectpicker" title="Choose one of the following..." id="extra_field_type" data-live-search="true" name="extra_field_type" data-actions-box="true">
                                @foreach($field_types as $field_type_value => $field_type)
                                    <option data-tokens="{{$field_type}}" value="{{$field_type_value}}" >{{$field_type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Extra Field Endpoint  (Needed in case of Field of Type "List", Refer to list on Top of page)</label>
                            <input type="text" name="extra_field_endpoint" id="extra_field_endpoint" class="form-control" placeholder="Extra Field Endpoint" value="{{ old('extra_field_endpoint') }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.dynamic_fields.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
        }
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
