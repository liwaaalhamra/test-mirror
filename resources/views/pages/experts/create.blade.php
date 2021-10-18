@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/experts') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Choose Region</label>
                            <select class="form-control selectpicker" title="Choose..." required  data-live-search="true" id="region_id" name="region_id">
                                @foreach($regions as $item)
                                    <option data-tokens="{{$item->name}}" value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Choose Area</label>
                            <select class="form-control selectpicker" title="Choose..." required disabled  data-live-search="true" id="area_id" name="area_id">
                                <option data-tokens="" value="" selected>Choose Area</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" required class="form-control" placeholder="Name" value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" required class="form-control" placeholder="Phone Number" value="{{ old('phone_number') }}">
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.experts.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $('#region_id').on('change', function () {
            if (this.value != '') {
                param = '/' + this.value ;
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: location.href.split("experts")[0]+ 'get_areas' + param,
                    success: function (response) {
                        //console.log(response);
                        $("#area_id").prop('disabled', false);
                        $('#area_id').empty();
                        // $('#area_id').append($('<option>').text('Choose Area ...').attr('value', ''));
                        $.each(response, function (i, value) {
                            $('#area_id').append($('<option>').text(value).attr('value', i));
                        });
                        $('#area_id').selectpicker('refresh');
                    }
                });

            } else {
                $("#area_id").prop('disabled', true);
                $('#area_id').selectpicker('refresh');
            }
        });
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
