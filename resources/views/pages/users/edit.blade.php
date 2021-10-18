@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/users/'. $user->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">{{$content_header}}</div>
                    <div class="panel-body">
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-2">--}}
                                {{--<label>Points To add/remove</label>--}}
                                {{--<input type="number" step="1" name="points" class="form-control" placeholder="Points" value="{{ old('points') }}">--}}
                            {{--</div>--}}
                            {{--<div class="form-group col-sm-5">--}}
                                {{--<label>Reason</label>--}}
                                {{--<input type="text" name="description" class="form-control" placeholder="Reason" value="{{old('description')}}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Profile Types</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="users" data-live-search="true" name="type_id" required>
                                        @foreach($types as $type)
                                            <option data-tokens="{{$type}}" @if($type == $user->access_type || ($type == 'regular' && !$user->access_type)) selected @endif value="{{$type}}" >{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            {{--<div class="form-group col-sm-3">--}}
                                {{--<label>Employee ID</label>--}}
                                {{--<input type="number" step="1" name="employee_id" class="form-control" required placeholder="Employee ID" value="{{ $user->employee_id }}">--}}
                            {{--</div>--}}
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Country Code</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="country_code" data-live-search="true" name="country_code" required>
                                        @foreach($countries as $country)
                                            <option data-content='<img src="{{$country->flag}}" width="17px" /> {{$country->name}} (+{{$country->calling_code}})' @if($user->country_code == $country->calling_code) selected @endif data-tokens="{{$country->name}}" value="+{{$country->calling_code}}" >{{$country->name}} (+{{$country->calling_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Mobile Number</label>
                                <input type="text" name="phone_number" required class="form-control" placeholder="Mobile Number" value="{{ $user->primary_phone }}">
                            </div>
                            {{--<div class="form-group col-sm-3">--}}
                            {{--<label>Blood Type</label>--}}
                            {{--<input type="text" name="blood_type" class="form-control" placeholder="Blood Type" value="{{ old('blood_type') }}">--}}
                            {{--</div>--}}
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ $user->email }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="First Name" value="{{ $user->first_name }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="Last Name" value="{{ $user->last_name }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Date Of Birth</label>
                                <div class='input-group date' id='dob' style="margin-bottom: 10px">
                                    <input type='text' name="dob" class="form-control" />
                                    <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Gender</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="gender" data-live-search="true" name="gender">
                                        @foreach($genders as $gender)
                                            <option data-tokens="{{$gender}}" @if($user->gender == $gender) selected @endif value="{{$gender}}" >{{$gender}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        {{--<div class="form-group col-sm-5">--}}
                            {{--<label>Full Name</label>--}}
                            {{--<input type="text" name="full_name" class="form-control" required placeholder="User's Full Name" value="{{ $user->full_name }}">--}}
                        {{--</div>--}}


                        {{--<div class="form-group col-sm-5">--}}
                            {{--<label>Mobile Number 2</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="mobile_number2" class="form-control" placeholder="Mobile Number 2" value="{{ $user->mobile_number2 }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-5">--}}
                            {{--<label>Department</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="department" class="form-control" placeholder="Department" value="{{ $user->department }}">--}}
                        {{--</div>--}}

                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>Casa</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="casa" class="form-control" placeholder="Casa" value="{{ $user->casa }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>City</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="city" class="form-control" placeholder="City" value="{{ $user->city }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>Address Details</label><small style="color: red">(Optional)</small>--}}
                            {{--<textarea name="address_details" rows="5" id="address_details" class="form-control">{{$user->address_details}}</textarea>--}}
                        {{--</div>--}}
                        <div class="form-group col-sm-4">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-dark">Back</a>
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="col-sm-6">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading font-bold"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Change Password</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--<small style="color: red">leave it empty if you don't want to change the password</small>--}}
                        {{--<div class="form-group">--}}
                            {{--<label>Old Password</label>--}}
                            {{--<input type="password" id="old_password" name="old_password" class="form-control">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label>New Password</label>--}}
                            {{--<input type="password" id="new_password" name="new_password" class="form-control" placeholder="The password must be at least 6 characters.">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<label>Confirm Password</label>--}}
                            {{--<input type="password" id="confirm_password" name="confirm_password" class="form-control">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </form>
    </div>
@endsection

@section('scripts')
    @parent

    <script>
        $(function () {

            var dob = "{{$user->dob}}";
            if(dob == ''){
                $('#dob').datetimepicker({format: 'YYYY/MM/DD'});
            }else{
                var dob_formatted = "{{$user->dob}}";
                $('#dob').datetimepicker({
                    defaultDate: dob_formatted,
                    format: 'YYYY/MM/DD'
                });
            }
        });
    var password = document.getElementById("new_password")
            , password_confirm = document.getElementById("confirm_password");

    function validatePassword(){
        if(password.value != password_confirm.value) {
            password_confirm.setCustomValidity("Passwords Don't Match");
        } else {
            password_confirm.setCustomValidity('');
        }
        if($('#new_password').val().length < 6){
            password.setCustomValidity("The password must be at least 6 characters.");
        }
    }

    password.onchange = validatePassword;
    password_confirm.onkeyup = validatePassword;
    </script>
@endsection
