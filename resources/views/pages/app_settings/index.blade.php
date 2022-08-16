@extends('vendor.layout')

@section('content')
    <div class="row">
        @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))

            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{env('APP_NAME')}} Settings</div>
                    <div class="panel-body">
                        <form action="{{ route('admin.app_settings') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>Version Update Title En</label>
                                    <input type="text" name="force_update_title_en" class="form-control" placeholder="Version Update Title" required @if(isset($settings['force_update_title_en'])) value="{{$settings['force_update_title_en']}}" @endif>
                                </div>
                                <div class="form-group col-sm-8">
                                    <label>Version Update Message En</label>
                                    <input type="text" name="force_update_message_en" class="form-control" placeholder="Version Update Message" required @if(isset($settings['force_update_message_en'])) value="{{$settings['force_update_message_en']}}" @endif>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>Version Update Title Ar</label>
                                    <input type="text" name="force_update_title_ar" class="form-control" placeholder="Version Update Title" required @if(isset($settings['force_update_title_ar'])) value="{{$settings['force_update_title_ar']}}" @endif>
                                </div>
                                <div class="form-group col-sm-8">
                                    <label>Version Update Message Ar</label>
                                    <input type="text" name="force_update_message_ar" class="form-control" placeholder="Version Update Message" required @if(isset($settings['force_update_message_ar'])) value="{{$settings['force_update_message_ar']}}" @endif>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>Android Version</label>
                                    <input type="text" name="android_version" class="form-control" placeholder="Android Version" required @if(isset($settings['android_version'])) value="{{$settings['android_version']}}" @endif>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Force Update Android App</label>
                                    <input name="android_force_update" type="checkbox" data-toggle="toggle" data-on="On" @if(isset($settings['android_force_update']) && $settings['android_force_update'] == 'on') checked @endif data-off="Off" data-onstyle="success" data-size="large">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label>iOS Version</label>
                                    <input type="text" name="ios_version" class="form-control" placeholder="iOS Version" required @if(isset($settings['ios_version'])) value="{{$settings['ios_version']}}" @endif>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Force Update iOS App</label>
                                    <input name="ios_force_update" type="checkbox" data-toggle="toggle" data-on="On" @if(isset($settings['ios_force_update']) && $settings['ios_force_update'] == 'on') checked @endif data-off="Off" data-onstyle="success" data-size="large">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
