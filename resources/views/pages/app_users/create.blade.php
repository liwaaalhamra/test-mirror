@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading font-bold">User</div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/users') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Choose Countries</label>
                            <select class="form-control selectpicker" multiple title="Choose..."  data-live-search="true" name="ids[]" required>
                                @foreach($countries as $country)
                                    <option data-tokens="{{$country->name}}" value="{{ $country->id }}">{{ ucwords($country->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" required placeholder="User's First Name" value="{{ old('first_name') }}">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" required placeholder="User's Last Name" value="{{ old('last_name') }}">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="Email" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="The password must be at least 6 characters." required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                        </div>


                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-dark">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
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
