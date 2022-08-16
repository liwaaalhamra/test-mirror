@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading ">Reset Password of <b>{{ucwords($user->name)}}</b></div>
                <div class="panel-body">
                    <form action="{{ secure_url('admin/cms_users/reset_password') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="user_id" value="{{$user->id}}">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="text" id="password" name="password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="text" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <button style="margin-left: 40px" data-toggle="tooltip" data-placement="right" title="Generate new password" type="button" id="generate" class="btn btn-success" data-clipboard-target="#password">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
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
        $('[data-toggle="tooltip"]').tooltip()
    });
    $(document).ready(function () {
        $('#generate').on('click', function () {
            generate();
        });
    });
    function generate() {
        $random_passwor = randomPassword(8);
        $('#password').val($random_passwor);
        $('#password_confirmation').val($random_passwor);
    }

    function randomPassword( len ) {
        var length = (len)?(len):(10);
        var string = "abcdefghijklmnopqrstuvwxyz"; //to upper
        var numeric = '0123456789';
        var punctuation = '!@#$%^&*()_+~`|}{[]\:;?><,./-=';
        var password = "";
        var character = "";
        var crunch = true;
        while( password.length<length ) {
            entity1 = Math.ceil(string.length * Math.random()*Math.random());
            entity2 = Math.ceil(numeric.length * Math.random()*Math.random());
            entity3 = Math.ceil(punctuation.length * Math.random()*Math.random());
            hold = string.charAt( entity1 );
            hold = (password.length%2==0)?(hold.toUpperCase()):(hold);
            character += hold;
            character += numeric.charAt( entity2 );
            character += punctuation.charAt( entity3 );
            password = character;
        }
        password=password.split('').sort(function(){return 0.5-Math.random()}).join('');
        return password.substr(0,len);
    }

</script>
@endsection
