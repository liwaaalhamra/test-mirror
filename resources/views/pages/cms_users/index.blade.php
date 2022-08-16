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
                <a href="{{ route('admin.cms_users.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
                @endif
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
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    {{--<th>Cost Center</th>--}}
                    <th>Email</th>
                    {{--<th>Password</th>--}}
                    {{--<th>Merchant Info</th>--}}
                    <th>Locked/Unlocked</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                    @foreach($users as $user)
                    <tr>
                        <td>{{ ucwords($user->first_name) }}</td>
                        <td>{{ ucwords($user->last_name) }}</td>
                        <td>@if($user->role) {{ ucwords($user->role->name) }} @else No Role Assigned yet @endif</td>
                        {{--<td>{{ $user->cost_center }}</td>--}}
                        <td>@if($user->email != null){{$user->email}}@else n/a @endif</td>
                        {{--<td>{{ $user->password_clear_text }}</td>--}}
                        {{--<td>--}}
                            {{--@if($user->merchant)--}}
                                {{--Name: <b>{{ $user->merchant->name }}</b> <br>--}}
                                {{--Mobile Number: <b>{{ $user->merchant->phone_number }}</b> <br>--}}
                                {{--Description: <b>{{ $user->merchant->description }}</b> <br>--}}
                                {{--Address: <b>{{ $user->merchant->address }}</b> <br>--}}
                                {{--@if($user->merchant->latitude != 0 && $user->merchant->longitude != 0)--}}
                                    {{--<span class="label label-info">--}}
                                    {{--<a target="_blank" href="http://maps.google.com/?q={{ $user->merchant->latitude }},{{ $user->merchant->longitude }}"> <i aria-hidden="true" style="display: inline-block" class="fa fa-street-view"></i>view Merchant Location</a>--}}
                                {{--</span>--}}
                                {{--@endif--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        <td>
                            @if(!$user->is_locked)
                                <span class="label label-success">Unlocked</span>
                            @else
                                <span class="label label-warning">Locked</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('Y/m/d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                            @if(!$user->is_locked)
                                <a href="{{ route('admin.cms_users.lock', $user->id) }}" class="btn btn-xs btn-danger">
                                    Lock
                                </a>
                            @else
                                <a href="{{ route('admin.cms_users.lock', $user->id) }}" class="btn btn-xs btn-success">
                                    Unlock
                                </a>
                            @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                            <a href="{{ route('admin.cms_users.edit', $user->id) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>
                            <a href="{{ route('admin.cms_users.reset', $user->id) }}" class="btn btn-xs btn-action"><i class="fa fa-unlock"> Reset password</i></a>
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/cms_users/'. $user->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
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
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.js" type="text/javascript"></script>
    <script src="https://rawgit.com/isocra/TableDnD/master/js/jquery.tablednd.js" type="text/javascript"></script>
    <script src="https://rawgit.com/wenzhixin/bootstrap-table/master/src/extensions/reorder-rows/bootstrap-table-reorder-rows.js" type="text/javascript"></script>
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
                order:[0,'asc'],
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
                order:[0,'asc'],
            });
        } );
        @endif

        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

    </script>
@endsection
