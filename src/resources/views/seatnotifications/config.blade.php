@section('title', "Seat Notifications")
@section('page_header', "Configuration Page")
@section('page_description', "Dashboard")

@extends('web::layouts.grids.12')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-comment"></i>

                    <h3 class="box-title">Notifications</h3>
                </div>
                <div class="box-body pad table-responsive">
                    <p>Various types of notifications and channels</p>

                </div>
                <!-- /.box -->
                <div class="box-footer">
                    <div class="col-md-3">
                        Installed version: <b>{{ config('seatnotifications.config.version') }}</b>
                    </div>
                    <div class="col-md-3">
                        Latest version:
                        <a href="https://packagist.org/packages/herpaderpaldent/seat-notifications">
                            <img src="https://poser.pugx.org/herpaderpaldent/seat-notifications/v/stable" alt="SeAT Notifications version" />
                        </a>
                    </div>
                    <div class="col-md-6"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($notification_channels as $channel)

            @include($channel)

        @endforeach
    </div>

    <div class="row">

        <div>


@endsection