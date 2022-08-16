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
                    <a href="{{ route('admin.promo_codes.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
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
                    <th>Code</th>
                    <th>Max uses</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Amount Value</th>
                    <th>Amount Type</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->max_uses }}</td>
                        <td>@if($item->start_date){{ \Carbon\Carbon::createFromTimestamp($item->start_date)->format('Y-m-d H:i:s') }} @endif</td>
                        <td>@if($item->end_date){{ \Carbon\Carbon::createFromTimestamp($item->end_date)->format('Y-m-d H:i:s') }} @endif</td>
                        <td>{{ $item->amount }}</td>
                        <td>{{ $item->type }}</td>
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Active</span></h4>
                            @else
                                <h4><span class="label label-warning">Inactive</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('publish',auth()->user()->role->hasAccess($section_name)))
                                @if($item->is_published)
                                    <a href="{{ route('admin.promo_codes.publish', $item->id) }}" class="btn btn-md btn-action">
                                        Un-publish
                                    </a>
                                @else
                                    <a href="{{ route('admin.promo_codes.publish', $item->id) }}" class="btn btn-md btn-action">
                                        Publish
                                    </a>
                                @endif
                            @endif
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                                <a href="{{ route('admin.promo_codes.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>
                            @endif
                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                                <form action="{{ secure_url('admin/promo_codes/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
                "pagingType": "full_numbers",
            });
        } );
        @endif
    </script>
@endsection

