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
                {{--<a href="{{ route('admin.users.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">--}}
                    {{--<i class="fa fa-plus"></i>Add {{$content_header}}--}}
                {{--</a>--}}
            </div>
            <div class="col-sm-3">
            </div>
            <div class="col-sm-4">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>User Info</th>
                    <th>Points</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                    @foreach($items as $item)
                    <tr>
                        <td>
                            @if($item->user != null)
                                Full Name: <b>{{$item->user->first_name}} {{$item->user->last_name}}</b> </br>
                                Email: <b>{{$item->user->email}}</b> </br>
                                Mobile Number: <b>{{$item->user->primary_phone}}</b> </br>
                                DOB: <b>{{$item->user->dob}}</b> </br>
                            @else
                                n/a
                            @endif
                        </td>
                        <td>@if($item->points != null){{$item->points}}@else n/a @endif</td>
                        <td>@if($item->description != null){{$item->description}}@else n/a @endif</td>
                        <td>@if($item->points != null)
                                @if($item->points == 0)
                                    <span class="label label-warning">REJECTED</span>

                                @else
                                    <span class="label label-success">ACCEPTED</span>
                                @endif
                            @else
                                <span class="label label-info">PENDING</span>
                            @endif
                        </td>
                        <td>
                            @if($item->image != null)
                                <img src="{{$item->image}}" width="100px" data-toggle="modal" data-target="#myModal_{{$item->id}}"  style="cursor: pointer;"/>
                                <div id="myModal_{{$item->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{$item->image}}" width="100%;">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($item->points == null)
                                <a href="{{ route('admin.user_gifts.action', [$item->id,'reject']) }}" class="btn btn-xs btn-danger">
                                    Reject
                                </a>
                                <a href="{{ route('admin.user_gifts.action', [$item->id,'accept']) }}" class="btn btn-xs btn-success">
                                    Accept
                                </a>
                            @endif
                            {{--<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>--}}

                            {{--<a href="{{ route('admin.users.reset', $user->id) }}" class="btn btn-xs btn-action"><i class="fa fa-unlock"> Reset password</i></a>--}}

                            {{--<form action="{{ secure_url('admin/users/'. $user->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
                                {{--{{ csrf_field() }}--}}
                                {{--{{ method_field('DELETE') }}--}}

                                {{--<button type="submit" class="btn btn-xs btn-danger">--}}
                                    {{--<i class="fa fa-trash"></i> Delete--}}
                                {{--</button>--}}
                            {{--</form>--}}
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
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.js" type="text/javascript"></script>
    <script src="https://rawgit.com/isocra/TableDnD/master/js/jquery.tablednd.js" type="text/javascript"></script>
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.js" type="text/javascript"></script>
    <script>
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
                order:[0,'asc'],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        } );
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

    </script>
@endsection
