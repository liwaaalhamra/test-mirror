@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/countries') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Flag <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="file" name="flag" class="form-control" required value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="name" class="form-control" required placeholder="Name" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Fullname <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="fullname" class="form-control" required placeholder="Name" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Published <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <select name="visibility" class="form-control" required>
                                    <option value="">Select Option</option>
                                    <option value="0">No</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Capital <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="capital" class="form-control" placeholder="Capital" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Country Code <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="country_code" class="form-control" placeholder="country_code" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Region Code</label>
                                <input type="text" name="region_code" class="form-control" placeholder="region_code" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Subregion Code</label>
                                <input type="text" name="sub_region_code" class="form-control" placeholder="sub_region_code" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Calling Code</label>
                                <input type="text" name="calling_code" class="form-control" placeholder="calling_code" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency</label>
                                <input type="text" name="currency" class="form-control" placeholder="currency" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Code</label>
                                <input type="text" name="currency_code" class="form-control" placeholder="currency_code" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Subunit</label>
                                <input type="text" name="currency_sub_unit" class="form-control" placeholder="currency_sub_unit" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Symbol</label>
                                <input type="text" name="currency_symbol" class="form-control" placeholder="currency_symbol" value="">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Decimals</label>
                                <input type="text" name="currency_decimals" class="form-control" placeholder="currency_decimals" value="">
                            </div>
                        </div>


                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.countries.index') }}" class="btn btn-sm btn-dark">Back</a>
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
