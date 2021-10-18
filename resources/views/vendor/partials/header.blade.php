<header id="header" class="app-header navbar" role="menu">
    <!-- navbar header -->
    <div class="navbar-header bg-dark">
        <button class="pull-right visible-xs dk" ui-toggle-class="show" target=".navbar-collapse">
            <i class="glyphicon glyphicon-cog"></i>
        </button>
        <button class="pull-right visible-xs" ui-toggle-class="off-screen" target=".app-aside" ui-scroll="app">
            <i class="glyphicon glyphicon-align-justify"></i>
        </button>
        {{--Brand--}}
        @include('vendor.partials.content-header.brand')
    </div>
    <!-- / navbar header -->

    <div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
        @include('vendor.partials.content-header.navbar-left')
        {{--Navbar right--}}
        @include('vendor.partials.content-header.navbar-right')
    </div>
{{--    @include('vendor.partials.menu')--}}
</header>
