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
                <a href="{{ route('admin.points.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
                @endif
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>User Full Name</th>
                    <th>User Job Title</th>
                    <th>User Cost Center</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Points</th>
                    {{--<th>Status</th>--}}
                    {{--<th>Date Added</th>--}}
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>
                            @if($item->user != null)
                                <b>{{$item->user->first_name}} {{$item->user->last_name}}</b>
                            @else
                                n/a
                            @endif
                        </td>
                        <td>
                            @if($item->user != null)
                                <b>{{$item->user->job_title}}</b>
                            @else
                                n/a
                            @endif
                        </td>
                        <td>
                            @if($item->user != null)
                                <b>{{$item->user->cost_center}}</b>
                            @else
                                n/a
                            @endif
                        </td>
                        <td>{{ $item->description }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->item_datetime)->format('Y-m-d H:i:s') }}</td>
                        <td><h3>{{$item->points}} Pts</h3></td>
                        {{--<td>--}}
                            {{--@if($item->is_published)--}}
                                {{--<h4><span class="label label-success">Online</span></h4>--}}
                            {{--@else--}}
                                {{--<h4><span class="label label-warning">Offline</span></h4>--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        {{--<td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>--}}
                        <td>
                            {{--@if($item->is_published)--}}
                                {{--<a href="{{ route('admin.jobs.publish', $item->id) }}" class="btn btn-md btn-warning">--}}
                                    {{--Un-publish--}}
                                {{--</a>--}}
                            {{--@else--}}
                                {{--<a href="{{ route('admin.jobs.publish', $item->id) }}" class="btn btn-md btn-success">--}}
                                    {{--Publish--}}
                                {{--</a>--}}
                            {{--@endif--}}
                            {{--<a href="{{ route('admin.points.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>--}}
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/points/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-md btn-danger">
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
                "pagingType": "full_numbers"
            });
        } );
        @endif
    </script>
@endsection

