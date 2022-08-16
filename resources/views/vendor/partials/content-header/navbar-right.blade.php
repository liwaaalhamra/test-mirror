<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle clear" data-toggle="dropdown">
            <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
                <img src="{{secure_asset('resources/assets/images/logo_new.png')}}">
                <i class="on md b-white bottom"></i>
            </span>
            <span class="hidden-sm hidden-md"></span>{{ucwords(auth()->user()->first_name)}} {{ucwords(auth()->user()->last_name)}}<b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="{{ route('admin.profile.index') }}">Profile</a>
            </li>
            <li>
                <a href="{{secure_url('/logout')}}">Logout</a>
            </li>
        </ul>
    </li>
</ul>
