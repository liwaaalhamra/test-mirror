@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/profile_types/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ $item->name }}">
                        </div>
                        <div class="form-group">
                            <label>Start Color (#FFFFFF)</label>
                            <input type="text" name="start_color" class="form-control" required placeholder="Start Color" value="{{ $item->start_color }}">
                        </div>
                        <div class="form-group">
                            <label>End Color (#FFFFFF)</label>
                            <input type="text" name="end_color" class="form-control" required placeholder="End Color" value="{{ $item->end_color }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.profile_types.index') }}" class="btn btn-sm btn-dark">Back</a>
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
        tinymce.init({
            selector: 'textarea',
            height: 200,
            menubar: false,
            plugins: [
            ],
            toolbar: 'undo redo  | styleselect | bold italic | bullist numlist',
            content_css: '//www.tinymce.com/css/codepen.min.css'
        });
    </script>
@endsection
