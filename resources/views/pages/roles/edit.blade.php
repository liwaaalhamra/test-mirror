@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/roles/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ $item->name }}">
                            </div>
                        </div>

                        <div class="row">
                            @if($item->slug != 'super_admin')
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
                                                        @php
                                                            $role_section_operations = \App\Models\Api\RoleAccess::whereSectionId($section->id)->whereRoleId($item->id)->first();
                                                            if($role_section_operations){
                                                                $role_section_operations = $role_section_operations->operations_list;
                                                            }else{
                                                                $role_section_operations = [];
                                                            }
                                                        @endphp
                                                        <input name="{{$section->id}}_{{$operation['meta_value']}}" @if(in_array($operation['meta_value'],$role_section_operations)) checked @endif type="checkbox" data-toggle="toggle" data-on="<i class='fa fa-unlock'></i> On" data-off="<i class='fa fa-lock'></i> Off" data-style="ios" data-onstyle="success" data-offstyle="danger" data-size="small">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <div class="form-group jumbotron" style="text-align: center">
                                    <h1>Permissions are not editable.</h1>
                                    <p>{{$item->name}} has access to all sections.</p>
                                </div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            $('[data-toggle="tooltip"]').tooltip()
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
