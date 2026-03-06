@extends('vendor.layout')

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a href="{{ route('admin.users.index') }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-users"></i>
                            </span>
                        </h3>
                        <h3>Registered Users</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $registered_users }}  
                                <small>
                                    <i class=" text-success">Users</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a href="{{ route('admin.users.index',['type' => 2]) }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-users"></i>
                            </span>
                        </h3>
                        <h3>Verified Users</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $verified_users }}  
                                <small>
                                    <i class=" text-success">Users</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="{{ route('admin.items.index') }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-tag"></i>
                            </span>
                        </h3>
                        <h3>Total Barters</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $total_barters }}  
                                <small>
                                    <i class=" text-success">Barters</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="{{ route('admin.items.index') }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-tag"></i>
                            </span>
                        </h3>
                        <h3>Pending Barters</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $pending_barters }}  
                                <small>
                                    <i class=" text-success">Barters</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="{{ route('admin.items.index') }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-tag"></i>
                            </span>
                        </h3>
                        <h3>rejected Barters</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $rejected_barters }}  
                                <small>
                                    <i class=" text-success">Barters</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="{{ route('admin.items.index') }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-tag"></i>
                            </span>
                        </h3>
                        <h3>Accepted Barters</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $accepted_barters }}  
                                <small>
                                    <i class=" text-success">Barters</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
            <a href="{{ route('admin.items.index') }}">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h3>
                            <span class="label label-success pull-right">
                                <i class="fas fa-tag"></i>
                            </span>
                        </h3>
                        <h3>Completed Barters</h3>
                    </div>
                    <div class="ibox-content" style="min-height: 133px">
                        <div class="font-bold">
                            <h2 class="no-margins counter-count">
                                {{ $completed_barters }}  
                                <small>
                                    <i class=" text-success">Barters</i>
                                </small>
                            </h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
