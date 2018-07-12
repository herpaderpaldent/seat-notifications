@section('title', "Seat Notifications")
@section('page_header', "Overview Page"))
@section('page_description', "Dashboard")

@extends('web::layouts.grids.12')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h3>Character Notifications</h3>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default disabled">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title">Industry Jobs</h3>
                </div>
                <div class="panel-body">
                    Get notified if your industry job has run to completion
                    <p class="margin">Slack user id f.e. U5KRYEK1V</p>
                    <div class="input-group input-group">
                        <input type="text" class="form-control">
                        <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat">Go!</button>
                    </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-4">
            <div class="panel panel-default disabled">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title">PI Notification</h3>
                </div>
                <div class="panel-body">
                    Get notified if your extractor has run to completion
                    <p class="margin">Slack user id f.e. U5KRYEK1V</p>
                    <div class="input-group input-group">
                        <input type="text" class="form-control disabled">
                        <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat">Go!</button>
                    </span>
                    </div>
                </div>
            </div>

        </div>

    </div>



    <div class="row">
        <div class="col-md-12">
            <h3>Corporation Notifications</h3>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title">Test</h3>
                </div>
                <div class="panel-body">
                    Get notified when a structure in your corporation unanchors
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control">
                        <span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat">Go!</button>
                    </span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>SeAT Notifications</h3>
        </div>

    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title">Refresh Token removed</h3>
                </div>
                <div class="panel-body">
                    Submit your Webhook to get notified immediately as soon as SeAT detects someone removing his
                    refresh token from your SeAT installation.
                    <form method="post" action="{{route('seatnotifications.post.webhook')}}">
                        {{csrf_field()}}
                        <input name="_method3" type="hidden" value="PATCH">
                        <input type="hidden" name="method" value="discord">
                        <input type="hidden" name="notification" value="RefreshTokenDeleted">
                        Enter your Webhook, note only the channel can be notified via Discord Webhook. No PM's possible.
                        <p class="margin">Discord Webhook</p>
                        <div class="input-group input-group-sm">
                            <input type="text" name="webhook" class="form-control" placeholder="if set show url....">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                    </form>
                    <form method="post" action="{{route('seatnotifications.post.webhook')}}">
                        {{csrf_field()}}
                        <input name="_method3" type="hidden" value="PATCH">
                        <input type="hidden" name="method" value="email">
                        <input type="hidden" name="notification" value="RefreshTokenDeleted">
                        Enter your email that you want the notifications to be delivered to.
                        <p class="margin">E-Mail</p>
                        <div class="input-group input-group-sm">
                            <input type="text" name="webhook" class="form-control" placeholder="if set show url....">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                    </form>
                    <form method="post" action="{{route('seatnotifications.post.webhook')}}">
                        {{csrf_field()}}
                        <input name="_method3" type="hidden" value="PATCH">
                        <input type="hidden" name="method" value="slack">
                        <input type="hidden" name="notification" value="RefreshTokenDeleted">
                        <p class="margin">Slack Channel</p>
                        <div class="input-group input-group-sm">
                            <input type="text" name="webhook" class="form-control" placeholder="if set show url....">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-flat">Go!</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

@endsection

