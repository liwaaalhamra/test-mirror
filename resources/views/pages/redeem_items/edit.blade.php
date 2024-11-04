@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/redeem_items/'.$item->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">Redeem Items</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" required id="title" class="form-control" placeholder="Title" value="{{ $item->title }}">
                        </div>
                        {{--<div class="form-group">--}}
                            {{--<label>Description</label>--}}
                            {{--<textarea name="description" required rows="4" class="form-control">{{$item->description}}</textarea>--}}
                        {{--</div>--}}
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" min="0" name="quantity" id="quantity" class="form-control" required placeholder="Quantity" value="{{ $item->quantity }}">
                        </div>
                        <div class="form-group">
                            <label>Points</label>
                            <input type="number" min="0" name="points" id="points" class="form-control" required placeholder="Points" value="{{ $item->points }}">
                        </div>
                        <div class="form-group">
                            <label>Upload Image </label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                        @if($item->image != null)
                            <img src="{{ url('uploads/images/redeem/'.$item->image) }}" width="100px" data-toggle="modal" data-target="#myModal"  style="cursor: pointer;"/>
                            <div id="myModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="{{ url('uploads/images/redeem/'.$item->image) }}" width="100%;">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <br><br>
                        @endif
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.redeem_items.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        //    tinymce.init({
        //        selector: 'textarea',
        //        height: 500,
        //        menubar: true,
        //        plugins: [
        //            'advlist autolink lists link image charmap print preview anchor',
        //            'searchreplace visualblocks code fullscreen',
        //            'insertdatetime media table contextmenu paste code'
        //        ],
        //        toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        //        content_css: '//www.tinymce.com/css/codepen.min.css'
        //    });
//        tinymce.init({
//            selector: 'textarea',
//            height: 200,
//            menubar: false,
//            plugins: [
//            ],
//            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
//            content_css: '//www.tinymce.com/css/codepen.min.css'
//        });
    </script>
@endsection
