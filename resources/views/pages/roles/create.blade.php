@extends('vendor.layout')

@section('content')
    <style>
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/roles') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name</label>
                                <input type="text" name="name" id="name" required class="form-control" placeholder="Name" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped b-t b-light">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center;">Menu Section</th>
                                        @foreach($operations as $operation)
                                            <th style="text-align: center;" data-container="body" data-toggle="tooltip" data-placement="top" title="{{$operation['info']}}">{{$operation['meta_name']}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($menu_sections as $section)
                                            <tr>
                                                <td style="text-align: center;">{{$section->label}}</td>
                                                @foreach($operations as $operation)
                                                    <td style="text-align: center;">
                                                    @if(in_array($operation['meta_value'],$section->available_operations_list))
                                                        <input name="{{$section->id}}_{{$operation['meta_value']}}" type="checkbox" data-toggle="toggle" data-on="<i class='fa fa-unlock'></i> On" data-off="<i class='fa fa-lock'></i> Off" data-style="ios" data-onstyle="success" data-offstyle="danger" data-size="small">
                                                    @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            $('[data-toggle="tooltip"]').tooltip()
        });
        // $( document ).ajaxComplete(function() {
        //     // Required for Bootstrap tooltips in DataTables
        //     $('[data-toggle="tooltip"]').tooltip({
        //         container : 'body'
        //     });
        // });
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
