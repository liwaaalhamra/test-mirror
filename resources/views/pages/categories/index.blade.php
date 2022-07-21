@extends('vendor.layout')

@section('content')
    <style>
        table.dataTable thead th {
            border-bottom: 0;
        }
        table.dataTable tfoot th {
            border-top: 0;
        }
        table.dataTable.no-footer {
            border-bottom: 0;
        }
    </style>
    <div class="panel panel-default">
        <div class="panel-heading">
            {{$content_header}}
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
                <a href="{{ route('admin.categories.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
                @endif
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Name English</th>
{{--                    <th>Name Arabic</th>--}}
                    <th>Type</th>
{{--                    <th>Is Supplier</th>--}}
                    <th>Image</th>
                    <th>Selected Image</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($categories as $item)
                    <tr>
                        <td># {{ $item->item_order }}</td>
                        <td>{{ $item->name_en }}</td>
{{--                        <td>{{ $item->name_ar }}</td>--}}
                        <td>{{ $item->type }}</td>
{{--                        <td>--}}
{{--                            @if($item->is_supplier)--}}
{{--                                <h4><span class="label label-success">YES</span></h4>--}}
{{--                            @else--}}
{{--                                <h4><span class="label label-warning">NO</span></h4>--}}
{{--                            @endif--}}
{{--                        </td>--}}
                        <td>
                            @if($item->image != null)
                                <img src="{{ secure_url('admin/medias/categories/'. $item->id.'/view_media') }}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{ secure_url('admin/medias/categories/'. $item->id.'/view_media') }}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($item->selected_image != null)
                                <img src="{{ secure_url('admin/medias/categories/'. $item->id.'/view_media/selected_image') }}" width="100px" data-toggle="modal" data-target="#myModal1_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal1_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{ secure_url('admin/medias/categories/'. $item->id.'/view_media/selected_image') }}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Online</span></h4>
                            @else
                                <h4><span class="label label-warning">Offline</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                @if($item->is_published)
                                    <a href="{{ route('admin.categories.publish', $item->id) }}" class="btn btn-md btn-warning">
                                        UnPublish
                                    </a>
                                @else
                                    <a href="{{ route('admin.categories.publish', $item->id) }}" class="btn btn-md btn-success">
                                        Publish
                                    </a>
                                @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                                <a href="{{ route('admin.categories.edit', $item->id) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"> Edit</i></a>
                                <a href="{{ route('admin.categories.sub_categories.index',$item->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-arrows-alt">  View Sub Categories</i></a>
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
{{--                                <form action="{{ secure_url('admin/categories/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
{{--                                    {{ csrf_field() }}--}}
{{--                                    {{ method_field('DELETE') }}--}}

{{--                                    <button type="submit" class="btn btn-xs btn-danger">--}}
{{--                                        <i class="fa fa-trash"></i> Delete--}}
{{--                                    </button>--}}
{{--                                </form>--}}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
        $(document).ready(function() {
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "bAutoWidth": true,
                "pagingType": "full_numbers",

                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });
        } );
        @else
        $(document).ready(function() {
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "bAutoWidth": true,
                "pagingType": "full_numbers",
            });
        } );
        @endif

        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

    </script>
@endsection

