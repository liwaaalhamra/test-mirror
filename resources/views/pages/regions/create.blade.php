@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/regions') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Country <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="country_id" required>
                                    @foreach($countries as $country)
                                        <option data-content='<img src="{{$country->flag}}" width="17px" /> {{$country->name}}' data-tokens="{{$country->name}}" value="{{ $country->id }}"> {{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="name_en" id="name_en" class="form-control" placeholder="Name English" value="{{ old('name_en') }}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" placeholder="Name Arabic" value="{{ old('name_ar') }}">
                            </div>
                        </div>


                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.regions.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
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
