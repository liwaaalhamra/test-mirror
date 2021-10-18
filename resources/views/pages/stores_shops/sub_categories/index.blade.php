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
            Sub Categories of <b>{{$category->name}}</b>
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.stores_shops.index') }}" class="btn m-b-xs btn-sm btn-primary btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                <a href="{{ route('admin.stores_shops.sub_categories.create', [$category->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
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
                    <th>Name</th>
                    <th>Created At</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td><h3>{{$item->name}}</h3></td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <a href="{{ secure_url('admin/sub_categories/'.$item->id.'/items') }}" class="btn btn-md btn-primary">View Items</a>
                            <a href="{{ route('admin.stores_shops.sub_categories.edit', [$category->id,$item->id]) }}" class="btn btn-md btn-info"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/stores_shops/'.$category->id.'/sub_categories/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-md btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
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

        $(document).ready(function() {
            $('#table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "bAutoWidth": true,
                "pagingType": "full_numbers"
            });
        } );

    </script>
@endsection
