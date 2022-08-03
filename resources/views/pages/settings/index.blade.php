@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">Change Admin Password</div>
                <div class="panel-body">
                    <form action="{{ route('admin.settings.changepassword') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>

        @if(in_array('edit',auth()->user()->role->hasAccess($section_name)))

            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{env('APP_NAME')}} Settings</div>
                    <div class="panel-body">
                        <form action="{{ route('admin.app_settings') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
{{--                            <div class="row">--}}
{{--                                <div class="form-group col-sm-4">--}}
{{--                                    <label>Delivery Charge</label>--}}
{{--                                    <input type="number" name="delivery_charge" class="form-control" placeholder="Delivery Charge" required @if(isset($settings['delivery_charge'])) value="{{$settings['delivery_charge']}}" @endif>--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-sm-4">--}}
{{--                                    <label>Delivery Threshold</label>--}}
{{--                                    <input type="number" name="delivery_threshold" class="form-control" placeholder="Delivery Threshold" required @if(isset($settings['delivery_threshold'])) value="{{$settings['delivery_threshold']}}" @endif>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>Post Cost</label>
                                    <input type="number" name="post_cost" class="form-control" placeholder="Post Cost" required @if(isset($settings['post_cost'])) value="{{$settings['post_cost']}}" @endif>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label>Post Validity Days</label>
                                    <input type="number" name="post_validity" class="form-control" placeholder="Post Validity Days" required @if(isset($settings['post_validity'])) value="{{$settings['post_validity']}}" @endif>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>Text us number</label>
                                    <input type="number" name="text_us_number" class="form-control" placeholder="Text us number" required @if(isset($settings['text_us_number'])) value="{{$settings['text_us_number']}}" @endif>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label>Call us number</label>
                                    <input type="number" name="call_us_number" class="form-control" placeholder="Call us number" required @if(isset($settings['call_us_number'])) value="{{$settings['call_us_number']}}" @endif>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <div class="imageupload panel panel-default">
                                        <div class="file-tab panel-body">
                                            <label class="btn btn-default btn-file">
                                                <span>Browse</span>
                                                <input type="file" name="faq_image" id="faq_image">
                                            </label>
                                            <button type="button" class="btn btn-default">Remove</button>
                                        </div>
                                        <div class="panel-heading clearfix" style="text-align: center">
                                            <h3 class="panel-title pull-left">FAQs Image</h3>
                                            <div class="btn-group">
                                                @if(isset($settings['faq_image']))
                                                    <img  alt="Image Preview"  src="{{ secure_url('public/uploads/images/settings/'.$settings['faq_image']) }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                    <div id="logo" class="modal fade" role="dialog">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h3 class="modal-title">FAQs Image</h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img src="{{ secure_url('public/uploads/images/settings/'.$settings['faq_image']) }}" width="100%;">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <br><br>
                                                @endif
                                            </div>
                                            <div class="btn-group pull-right">
                                                <button type="button" class="btn btn-default active">File</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="imageupload panel panel-default">
                                        <div class="file-tab panel-body">
                                            <label class="btn btn-default btn-file">
                                                <span>Browse</span>
                                                <input type="file" name="settings_image" id="settings_image">
                                            </label>
                                            <button type="button" class="btn btn-default">Remove</button>
                                        </div>
                                        <div class="panel-heading clearfix" style="text-align: center">
                                            <h3 class="panel-title pull-left">Settings Image</h3>
                                            <div class="btn-group">
                                                @if(isset($settings['settings_image']))
                                                    <img  alt="Image Preview"  src="{{ secure_url('public/uploads/images/settings/'.$settings['settings_image']) }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                    <div id="logo" class="modal fade" role="dialog">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h3 class="modal-title">Settings Image</h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img src="{{ secure_url('public/uploads/images/settings/'.$settings['settings_image']) }}" width="100%;">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <br><br>
                                                @endif
                                            </div>
                                            <div class="btn-group pull-right">
                                                <button type="button" class="btn btn-default active">File</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <div class="imageupload panel panel-default">
                                        <div class="file-tab panel-body">
                                            <label class="btn btn-default btn-file">
                                                <span>Browse</span>
                                                <input type="file" name="contact_us_image" id="contact_us_image">
                                            </label>
                                            <button type="button" class="btn btn-default">Remove</button>
                                        </div>
                                        <div class="panel-heading clearfix" style="text-align: center">
                                            <h3 class="panel-title pull-left">Contact Us Image</h3>
                                            <div class="btn-group">
                                                @if(isset($settings['contact_us_image']))
                                                    <img  alt="Image Preview"  src="{{ secure_url('public/uploads/images/settings/'.$settings['contact_us_image']) }}" class="thumbnail" data-toggle="modal" data-target="#logo"  style="cursor: pointer;max-width:200px;max-height: 200px;margin-bottom: 0px"/>
                                                    <div id="logo" class="modal fade" role="dialog">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h3 class="modal-title">Contact Us Image</h3>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img src="{{ secure_url('public/uploads/images/settings/'.$settings['contact_us_image']) }}" width="100%;">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <br><br>
                                                @endif
                                            </div>
                                            <div class="btn-group pull-right">
                                                <button type="button" class="btn btn-default active">File</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-sm-4">
            {{--<iframe src="https://sandbox-kw.flickapp.me/embed/tracking/3KDDYNYU961LVDEP" border="0"></iframe>--}}
{{--            <form action="{{ secure_url('admin/import_items') }}" method="POST" enctype="multipart/form-data">--}}
{{--                {{ csrf_field() }}--}}
{{--                <input type="file" name="excel" />--}}
{{--                <button type="submit" class="btn m-b-xs btn-sm btn-success btn-addon">--}}
{{--                    <i class="fa fa-upload"></i>Upload Sheets--}}
{{--                </button>--}}
{{--            </form>--}}
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        var $imageupload = $('.imageupload');
        $imageupload.imageupload();
    </script>
@endsection
