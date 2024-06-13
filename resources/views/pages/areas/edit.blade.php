@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/areas/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Region <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="region_id" required>
                                    @foreach($regions as $region)
                                        <option data-tokens="{{$region->name}}" @if($item->country_id == $region->id) selected @endif value="{{ $region->id }}">{{$region->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="Name" value="{{ $item->name_en }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" placeholder="Name" value="{{ $item->name_ar }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.areas.index') }}" class="btn btn-sm btn-dark">Back</a>
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
