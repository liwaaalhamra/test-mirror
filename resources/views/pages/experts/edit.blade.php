@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/experts/'.$item->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        <div class="form-group ">
                            <label>Choose Region</label>
                            <select class="form-control selectpicker" title="Choose..." required  data-live-search="true" id="region_id" name="region_id">
                                @foreach($regions as $region)
                                    <option data-tokens="{{$region->name}}" @if($item->region && $item->region->id == $region->id) selected @endif value="{{ $region->id }}">{{ ucwords($region->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ">
                            <label>Choose Area</label>
                            <select class="form-control selectpicker" title="Choose..." required  data-live-search="true" id="area_id" name="area_id">
                                @foreach($areas as $area)
                                    <option data-tokens="{{$area->name}}" @if($item->area && $item->area->id == $area->id) selected @endif value="{{ $area->id }}">{{ ucwords($area->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ $item->name }}">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" required placeholder="Phone Number" value="{{ $item->phone_number }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ route('admin.experts.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
        </form>
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
