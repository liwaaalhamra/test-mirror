<a href="{{secure_url('/admin')}}" class="navbar-brand text-lt">
    {{--<i class="fa fa-car"></i>--}}
    <img src="{{env('APP_ENV') == 'local' ? asset('resources/assets/images/logo_new.png') : secure_asset('resources/assets/images/logo_new.png')}}">
    <span class="hidden-folded m-l-xs">{{env('APP_NAME')}}</span>
</a>
