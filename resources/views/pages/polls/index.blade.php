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
                <a href="{{ route('admin.polls.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                <tr>
                    <th>Question</th>
                    <th>Points</th>
                    <th>Status</th>
                    <th>Start DateTime</th>
                    <th>End DateTime</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->question }}</td>
                        <td>{{ $item->points }} Pts</td>
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Active</span></h4>
                            @else
                                <h4><span class="label label-warning">Not Active</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->start_datetime)->format('Y-m-d H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->end_datetime)->format('Y-m-d H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($item->is_published)
                                <a href="{{ route('admin.polls.publish', $item->id) }}" class="btn btn-md btn-warning">
                                    Deactivate
                                </a>
                            @else
                                <a href="{{ route('admin.polls.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Set As Active
                                </a>
                            @endif
                            <a href="{{ route('admin.polls.options.index', $item->id) }}" class="btn btn-md btn-primary"><i class="fa fa-arrows-alt">Options</i></a>
                            <a href="{{ route('admin.polls.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/polls/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
    </script>
@endsection

