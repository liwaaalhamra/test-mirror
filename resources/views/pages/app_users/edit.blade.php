@extends('vendor.layout')

@section('content')
    <div class="row">
        <form action="{{ secure_url('admin/users/'. $user->id) }}" method="post">
            <input type="hidden" value="PUT" name="_method">
            {{ csrf_field() }}
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold">User</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label>Choose Countries</label>
                            <select class="form-control selectpicker" multiple title="Choose..."  data-live-search="true" name="ids[]" required>
                                @foreach($countries as $country)
                                    <option data-tokens="{{$country->name}}" value="{{ $country->id }}" @if(in_array($country->id,$countries_ids)) selected @endif>{{ ucwords($country->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="User's First Name" value="{{ $user->first_name }}">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="User's Last Name" value="{{ $user->last_name }}">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ $user->email }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading font-bold"><i class="fa fa-unlock-alt" aria-hidden="true"></i> Change Password</div>
                    <div class="panel-body">
                        <small style="color: red">leave it empty if you don't want to change the password</small>
                        <div class="form-group">
                            <label>Old Password</label>
                            <input type="password" id="old_password" name="old_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="The password must be at least 6 characters.">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
//        $(".phone").mask("99999999999");
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
