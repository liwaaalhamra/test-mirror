@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Menu of {{ucwords($restaurant->name_en)}}</div>
                <div class="panel-body">
                    <form id="submit_form" action="{{ route('admin.restaurants.menu.store',$restaurant->id) }}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name English</label>
                                <input type="text" name="name_en" id="name_en" class="form-control" required placeholder="Name English" value="{{ old('name_en') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Name Arabic</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control" required placeholder="Name Arabic" value="{{ old('name_ar') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Subtitle English</label>
                                <input type="text" name="subtitle_en" id="subtitle_en" class="form-control" required placeholder="Subtitle English" value="{{ old('subtitle_en') }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Subtitle Arabic</label>
                                <input type="text" name="subtitle_ar" id="subtitle_ar" class="form-control" required placeholder="Subtitle Arabic" value="{{ old('subtitle_ar') }}">
                            </div>
                        </div>


                        <button type="submit" id="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.restaurants.menu.index',$restaurant->id) }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
    </script>
@endsection
