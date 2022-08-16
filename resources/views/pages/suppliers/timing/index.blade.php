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
            Working Hours of <b>{{$restaurant->name_en}}</b>
        </div>
        <div class="row wrapper">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.restaurants.index') }}" class="btn m-b-xs btn-sm btn-primary btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                @if(in_array('add',auth()->user()->role->hasAccess($section_name)))
                    <a href="{{ route('admin.restaurants.timing.create', [$restaurant->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
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
            <table  id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Day</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Created At</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td><h3>{{ucwords($item->type)}}</h3></td>
                        <td>{{ucwords($item->day_name)}}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->time_from)->format('H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->time_to)->format('H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))
                            <a href="{{ route('admin.restaurants.timing.edit', [$restaurant->id,$item->id]) }}" class="btn btn-xs btn-action"><i class="fa fa-edit"> Edit</i></a>
                            @endif

                            @if(in_array('delete',auth()->user()->role->hasAccess($section_name)))
                            <form action="{{ secure_url('admin/restaurants/'.$restaurant->id.'/timing/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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
                    // order: [0,'asc'],
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
                    // order: [0,'asc'],
                    "pagingType": "full_numbers",
                });
            } );
        @endif

    </script>
@endsection
