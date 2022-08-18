@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">{{$content_header}}</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/users') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Profile Types</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="users" data-live-search="true" name="type_id" required>
                                        @foreach($types as $type)
                                            <option data-tokens="{{$type}}" value="{{$type}}" >{{$type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            {{--<div class="form-group col-sm-3">--}}
                                {{--<label>Employee ID</label>--}}
                                {{--<input type="number" step="1" name="employee_id" class="form-control" required placeholder="Employee ID" value="{{ old('employee_id') }}">--}}
                            {{--</div>--}}
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="control-label" >Country Code</label>
                                <div class="form-group ">
                                    <select class="form-control selectpicker" id="country_code" data-live-search="true" name="country_code" required>
                                        @foreach($countries as $country)
                                            <option data-content='<img src="{{$country->flag}}" width="17px" /> {{$country->name}} (+{{$country->calling_code}})' data-tokens="{{$country->name}}" value="+{{$country->calling_code}}" >{{$country->name}} (+{{$country->calling_code}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Mobile Number</label>
                                <input type="text" name="phone_number" required class="form-control" placeholder="Mobile Number" value="{{ old('phone_number') }}">
                            </div>
                            {{--<div class="form-group col-sm-3">--}}
                            {{--<label>Blood Type</label>--}}
                            {{--<input type="text" name="blood_type" class="form-control" placeholder="Blood Type" value="{{ old('blood_type') }}">--}}
                            {{--</div>--}}
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="First Name" value="{{ old('first_name') }}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="Last Name" value="{{ old('last_name') }}">
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
                                            <option data-tokens="{{$gender}}" value="{{$gender}}" >{{$gender}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>Login Code</label>--}}
                            {{--<input type="text" name="code" class="form-control" required placeholder="User's Login Code" value="{{ old('code') }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>Full Name</label>--}}
                            {{--<input type="text" name="full_name" class="form-control" required placeholder="User's Full Name" value="{{ old('full_name') }}">--}}
                        {{--</div>--}}


                        {{--<div class="form-group col-sm-5">--}}
                            {{--<label>Mobile Number 2</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="mobile_number2" class="form-control" placeholder="Mobile Number 2" value="{{ old('mobile_number2') }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-5">--}}
                            {{--<label>Department</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="department" class="form-control" placeholder="Department" value="{{ old('department') }}">--}}
                        {{--</div>--}}

                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>Caza</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="casa" class="form-control" placeholder="Casa" value="{{ old('casa') }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>City</label><small style="color: red">(Optional)</small>--}}
                            {{--<input type="text" name="city" class="form-control" placeholder="City" value="{{ old('city') }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group col-sm-4">--}}
                            {{--<label>Address Details</label><small style="color: red">(Optional)</small>--}}
                            {{--<textarea name="address_details" rows="10" class="form-control"></textarea>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-sm-6">--}}
                                {{--<label>Password</label>--}}
                                {{--<input type="password" id="password" name="password" class="form-control" placeholder="The password must be at least 6 characters." required>--}}
                            {{--</div>--}}
                            {{--<div class="form-group col-sm-6">--}}
                                {{--<label>Confirm Password</label>--}}
                                {{--<input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="form-group col-sm-6">
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-dark">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
    $(function () {
        $('#date_employment').datetimepicker();
        $('#registration_date').datetimepicker();
        $('#dob').datetimepicker({format: 'YYYY/MM/DD'});
    });
//        $(".phone").mask("99999999999");
    var password = document.getElementById("password")
            , password_confirm = document.getElementById("password_confirm");

    function validatePassword(){
        if(password.value != password_confirm.value) {
            password_confirm.setCustomValidity("Passwords Don't Match");
        } else {
            password_confirm.setCustomValidity('');
        }
    }

    password.onchange = validatePassword;
    password_confirm.onkeyup = validatePassword;
    </script>
@endsection
