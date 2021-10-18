@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ route('admin.cost_levels.update',$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" required placeholder="Amount" value="{{ $item->amount }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.cost_levels.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
