@section('title', "Seat Notifications")
@section('page_header', "Configuration Page"))
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
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($notification_channels as $channel)

            {!! $channel !!}

        @endforeach
    </div>


@endsection