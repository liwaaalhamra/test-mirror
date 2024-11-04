@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/countries/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        {{-- <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Choose Country</label>
                                <select class="form-control selectpicker" title="Choose..."  data-live-search="true" name="country_id" required>
                                    @foreach($countries as $country)
                                        <option data-tokens="{{$country->name}}" @if($item->country_id == $country->id) selected @endif value="{{ $country->id }}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Flag</label>
                                <img src="{{$item->flag}}"/>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="name" class="form-control" required placeholder="Name" value="{{$item->name}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Fullname <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="full_name" class="form-control" required placeholder="Name" value="{{$item->full_name}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Published <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <select name="visibility" class="form-control" required>
                                    <option value="">Select Option</option>
                                    <option value="0" @if($item->visibility == 0) selected @endif>No</option>
                                    <option value="1" @if($item->visibility == 1) selected @endif>YES</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Capital <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="capital" class="form-control" placeholder="Capital" value="{{$item->capital}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Country Code <p style="color: red; display: inline-block; line-height: 0.1rem">*</p></label>
                                <input type="text" name="country_code" class="form-control" placeholder="country_code" value="{{$item->country_code}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Region Code</label>
                                <input type="text" name="region_code" class="form-control" placeholder="region_code" value="{{$item->region_code}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Subregion Code</label>
                                <input type="text" name="sub_region_code" class="form-control" placeholder="sub_region_code" value="{{$item->sub_region_code}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Calling Code</label>
                                <input type="text" name="calling_code" class="form-control" placeholder="calling_code" value="{{$item->calling_code}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency</label>
                                <input type="text" name="currency" class="form-control" placeholder="currency" value="{{$item->currency}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Code</label>
                                <input type="text" name="currency_code" class="form-control" placeholder="currency_code" value="{{$item->currency_code}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Subunit</label>
                                <input type="text" name="currency_sub_unit" class="form-control" placeholder="currency_sub_unit" value="{{$item->currency_sub_unit}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Symbol</label>
                                <input type="text" name="currency_symbol" class="form-control" placeholder="currency_symbol" value="{{$item->currency_symbol}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Currency Decimals</label>
                                <input type="text" name="currency_decimals" class="form-control" placeholder="currency_decimals" value="{{$item->currency_decimals}}">
                            </div>
                        </div>


                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.countries.index') }}" class="btn btn-sm btn-dark">Back</a>
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
