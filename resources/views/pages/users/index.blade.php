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
        
        <form action="{{ secure_url('admin/users') }}" method="get" role="search" id="filter_form" >
            <div class="row wrapper">
                <div class="col-sm-2">                    
                    <label><strong>Register</strong></label>
                    <div class='input-group' >

                    @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
                    <a href="{{ route('admin.users.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                        <i class="fa fa-plus"></i>Register {{$content_header}}
                    </a>
                    @endif
                </div>
                </div>
                <div class="col-sm-3">
                    <label><strong>Filter by Status</strong></label>
                    <select class="form-control selectpicker" id="type" data-live-search="true" name="type" required >
                        <option data-tokens="All" @if($selected_type == '1') selected @endif value="1" >All Customers</option>
                        <option data-tokens="Registered" @if($selected_type == '2') selected @endif value="2" >All Registered Customers</option>
                        <option data-tokens="Registered Available" @if($selected_type == '3') selected @endif value="3" >Registered + Available Customers</option>
                        <option data-tokens="Registered Blocked" @if($selected_type == '4') selected @endif value="4" >Registered + Blocked Customers</option>
                        <option data-tokens="Not Verified" @if($selected_type == '5') selected @endif value="5" >Not Verified Customers</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Date From</label>
                    <div class='input-group date' id='from'>
                        <input type='text' name="min" id="min" class="form-control" />
                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Date To</label>
                    <div class='input-group date' id='to'>
                        <input type='text' name="max" id="max" class="form-control" />
                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                    </div>
                </div>
                <div class="col-sm-2">                    
                    <label><strong></strong></label>
                    <div class='input-group'>

                    <button type="submit" class="btn btn-lg btn-action">GO</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Profile Type</th>
                    <th>Mobile Number</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Registration Status</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @if($user->primary_phone || $user->email)
                            <tr>
                                <td>{{$user->id}}</td>

                                <td>{{ ucwords($user->full_name) }}</td>
                                <td>{{$user->access_type}}</td>
                                <td>@if($user->primary_phone != null){{$user->primary_phone}}@else n/a @endif</td>
                                <td>@if($user->dob != null){{$user->dob}}@else n/a @endif</td>
                                <td>@if($user->gender != null){{$user->gender}}@else n/a @endif</td>
                                <td>@if($user->email != null){{$user->email}}@else n/a @endif</td>
                                <td>
                                    @if($user->is_verified)
                                        <h3> <span class="label label-success">Registered</span></h3>
                                    @else
                                        <h3> <span class="label label-warning">Not Verified</span></h3>
                                    @endif
                                </td>
                                <td>
                                    @if(!$user->is_blocked)
                                        <h3><span class="label label-success">Active</span></h3>
                                    @else
                                        <h3><span class="label label-warning">Blocked</span></h3>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('Y/m/d H:i:s') }}</td>
                                <td>
                                    @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                    @if(!$user->is_blocked)
                                        <a href="{{ route('admin.users.block', $user->id) }}" class="btn btn-xs btn-danger">
                                            Block
                                        </a>
                                    @else
                                        <a href="{{ route('admin.users.block', $user->id) }}" class="btn btn-xs btn-success">
                                            UnBlock
                                        </a>
                                    @endif
                                    @endif
                                    @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>
                                        <a href="{{ route('admin.users.reset', $user->id) }}" class="btn btn-xs btn-action"><i class="fa fa-unlock"> Reset password</i></a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent

    <script>
        $(function() {
            var from_datetime = "{{$from}}";
            var to_datetime = "{{$to}}";
            if (!from_datetime.trim()) {
                $('#from').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
            } else {
                var from_formatted = "{{\Carbon\Carbon::createFromTimestamp($from)->format('Y-m-d')}}";
                $('#from').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: from_formatted
                });
            }
            if (!to_datetime.trim()) {
                $('#to').datetimepicker({
                    format: 'YYYY/MM/DD'
                });
            } else {
                var from_formatted = "{{\Carbon\Carbon::createFromTimestamp($to)->format('Y-m-d')}}";
                $('#to').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: from_formatted
                });
            }
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
                "pagingType": "full_numbers",
                order:[9,'desc'],
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
                "pagingType": "full_numbers",
                order:[9,'desc'],
            });
        } );
        @endif
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });

    </script>
@endsection
