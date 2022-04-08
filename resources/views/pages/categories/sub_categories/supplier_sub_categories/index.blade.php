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
            Sub 2 of <b>{{$category->name_en}}</b>
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.categories.sub_categories.index',$category->category_id) }}" class="btn m-b-xs btn-sm btn-primary btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
                    <a href="{{ route('admin.sub_categories.supplier_sub_categories.create', [$category->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                        <i class="fa fa-plus"></i>Add Sub 2
                    </a>
                @endif
            </div>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="table-responsive">
            <table  id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Order</th>
                    <th>Name En</th>
{{--                    <th>Name Ar</th>--}}
{{--                    <th>Description En</th>--}}
{{--                    <th>Description Ar</th>--}}
                    <th>Status</th>
                    <th>Created At</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>#{{$item->item_order}}</td>
                        <td>{{$item->name_en}}</td>
{{--                        <td>{{$item->name_ar}}</td>--}}
{{--                        <td>{!! $item->description_en !!}</td>--}}
{{--                        <td>{!! $item->description_ar !!}</td>--}}
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
                                @if(!$item->is_published)
                                    <a href="{{ route('admin.sub_categories.supplier_sub_categories.publish', $item->id) }}" class="btn btn-md btn-success">
                                        Publish
                                    </a>
                                @else
                                    <a href="{{ route('admin.sub_categories.supplier_sub_categories.publish', $item->id) }}" class="btn btn-md btn-warning">
                                        UnPublish
                                    </a>
                                @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
{{--                                <a href="{{ route('admin.sub_categories.supplier_sub_categories.edit', [$category->id,$item->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"> Edit</i></a>--}}
                            @endif

                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
{{--                            <form action="{{ secure_url('admin/sub_categories/'.$category->id.'/supplier_sub_categories/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
{{--                                {{ csrf_field() }}--}}
{{--                                {{ method_field('DELETE') }}--}}

{{--                                <button type="submit" class="btn btn-xs btn-danger">--}}
{{--                                    <i class="fa fa-trash"></i> Delete--}}
{{--                                </button>--}}
{{--                            </form>--}}
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
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

        @if(in_array('export',auth()->user()->role->hasAccess($section_name)))
            $(document).ready(function() {
                $('.table').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "pageLength": 50,
                    "bFilter": true,
                    "bInfo": true,
                    "ordering": true,
                    "bAutoWidth": true,
                    order: [0,'asc'],
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
                    "ordering": true,
                    "bAutoWidth": true,
                    order: [0,'asc'],
                    "pagingType": "full_numbers",
                });
            } );
        @endif

    </script>
@endsection
