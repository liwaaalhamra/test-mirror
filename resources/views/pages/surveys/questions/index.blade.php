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
                <a href="{{ route('admin.surveys.index') }}" class="btn m-b-xs btn-sm btn-default btn-addon">
                    <i class="fa fa-arrow-circle-left"></i>Back
                </a>
                <a href="{{ route('admin.surveys.questions.create', [$category->id]) }}" class="btn m-b-xs btn-sm btn-default btn-addon">
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
                    <th>Question</th>
                    {{--<th>Type</th>--}}
                    <th>Published</th>
                    <th>Date Added</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr >
                        <td>{{ ucwords($item->question) }}</td>
                        {{--<td>{{ $item->type }}</td>--}}
                        <td>
                            @if($item->is_published)
                                <h4><span class="label label-success">YES</span></h4>
                            @else
                                <h4><span class="label label-warning">No</span></h4>
                            @endif
                        </td>

                        <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if(!$item->is_published)
                                <a href="{{ route('admin.survey_questions.publish', $item->id) }}" class="btn btn-md btn-success">
                                    Publish
                                </a>
                            @else
                                <a href="{{ route('admin.survey_questions.publish', $item->id) }}" class="btn btn-md btn-default">
                                    UnPublish
                                </a>
                            @endif
                            @if($item->type != 'free_text')
                                <a href="{{ route('admin.survey_questions.answers.index', $item->id) }}" class="btn btn-md btn-primary"><i class="fa fa-arrows-alt"> Answers</i></a>
                            @endif
                                <a href="{{ route('admin.surveys.questions.edit', [$category->id,$item->id]) }}" class="btn btn-md btn-info"><i class="fa fa-edit"> Edit</i></a>
                            {{--<form action="{{ secure_url('admin/surveys/'.$category->id.'/questions/'. $item->id) }}" method="POST" class="pull-left delete" style="margin-right: 3px">--}}
                                {{--{{ csrf_field() }}--}}
                                {{--{{ method_field('DELETE') }}--}}

                                {{--<button type="submit" class="btn btn-md btn-danger">--}}
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
    <script>
        $(".delete").on("submit", function(){
            return confirm("Are you sure you want to delete this?");
        });
        $(".publish").on("submit", function(){
            return confirm("Are you sure you want to set this competitor as WINNER?");
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

