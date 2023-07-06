@extends('vendor.layout')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.css"/>
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.css"/>

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
                    <a href="{{ route('admin.lists.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                        <i class="fa fa-plus"></i>Add {{$content_header}}
                    </a>
                @endif
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->field_name }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                            <a href="{{ route('admin.lists.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>
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
        $(document).ready(function() {
            $('.table').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "pageLength": 50,
                "bFilter": true,
                "bInfo": true,
                "ordering": true,
                "bAutoWidth": true,
                order: [0,'desc'],
                "pagingType": "full_numbers",

                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });
        } );
    </script>
@endsection

