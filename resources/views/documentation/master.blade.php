<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>Dashboard | Tedmob</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('documentation.styles')
    {{--<link rel="shortcut icon" href="favicon.ico" />--}}
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-sidebar-closed-hide-logo @if(isset($page_settings['grey_bg'])) page-container-bg-solid @endif page-content-white @if(isset($page_settings['sidebar_closed'])) page-sidebar-closed @endif page-sidebar-fixed">

{{--@include('partials.topmenu')--}}

        <!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"> </div>
<!-- END HEADER & CONTENT DIVIDER -->
<!-- BEGIN CONTAINER -->
<div class="page-container">
    {{--@include('partials.sidemenu')--}}
    @include('documentation.partials.content')
</div>
<!-- END CONTAINER -->
{{--@include('partials.footer')--}}

@include('documentation.scripts')
</body>
</html>