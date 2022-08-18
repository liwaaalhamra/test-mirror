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
                <a href="{{ route('admin.memos.create') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-plus"></i>Add {{$content_header}}
                </a>
            </div>
            <div class="col-sm-3">
            </div>
        </div>
        <div class="table-responsive">
            <table id="table" class="table table-hover b-t b-light">
                <thead>
                <tr>
                    <th>Caption</th>
                    <th>Description</th>
                    <th>Memo Date</th>
                    <th>PDF</th>
                    {{--<th>Link</th>--}}
                    <th>Status</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>{{ ucwords($item->caption) }}</td>
                        <td>
                            {!!  $item->description !!}<br>
                        </td>
                        <td>@if($item->memo_datetime){{ \Carbon\Carbon::createFromTimestamp($item->memo_datetime)->format('Y-m-d H:i:s') }}@endif</td>
                        <td>
                            @if($item->pdf != null)
                                <a class="btn btn-primary view-pdf" data-toggle="modal" data-target="#pdfForm{{$item->id}}" >View PDF</a>
                                <div class="modal fade" id="pdfForm{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
                                    <div class="modal-dialog centerModal" role="document" style="position: fixed;top: 50%;left: 50%;transform: translate(-50%, -50%);">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <object type="application/pdf" data="{{$item->pdf}}" width="100%" height="500">No Support</object>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        {{--<td>{{ $item->link }}</td>--}}
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">Published</span></h4>
                            @else
                                <h4><span class="label label-danger">Un-published</span></h4>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($item->is_published)
                                <a href="{{ route('admin.memos.publish', $item->id) }}" class="btn btn-md btn-warning">
                                    Un-publish
                                </a>
                            @else
                                <a href="{{ route('admin.memos.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Publish
                                </a>
                            @endif
                            <a href="{{ route('admin.memos.edit', $item->id) }}" class="btn btn-md btn-action"><i class="fa fa-edit"> Edit</i></a>
                            <form action="{{ secure_url('admin/memos/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">
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

