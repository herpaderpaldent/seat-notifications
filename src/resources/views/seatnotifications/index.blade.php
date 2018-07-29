@section('title', "Seat Notifications")
@section('page_header', "Overview Page"))
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
                    <table class="table table-bordered text-center">
                        <tbody><tr>
                            <th>Notification</th>
                            <th>Discord</th>
                            <th>Slack</th>
                        </tr>
                        <tr>
                            <td>
                                Refresh Token Deleted
                            </td>
                            <td>
                                @if(\Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification::where('notification','RefreshTokenDeleted')->where('method','discord')->get()->count() === 0)
                                    <button type="button" class="btn btn-block btn-default" data-toggle="modal" data-target="#modal-refreshtokendeleted-discord">Subscribe</button>
                                @else
                                    <a href="{{route('seatnotifications.delete.seat.notification', ['method' => 'discord', 'notification' => 'RefreshTokenDeleted'])}}" type="button" class="btn btn-block btn-danger">Unsubscribe</a>
                                @endif
                            </td>
                            <td>
                                @if(empty(setting("slack_webhook", true)))
                                    <button type="button" class="btn btn-block btn-default disabled" data-toggle="tooltip" data-placement="bottom" title="Ask a superuser to add a slack webhook">Subscribe</button>
                                @elseif (\Herpaderpaldent\Seat\SeatNotifications\Models\Seatnotification::where('notification','RefreshTokenDeleted')->where('method','slack')->get()->count() === 0)
                                    {{--TODO: This elseif must be refactored with logic, 1 per channel but many per private--}}
                                    <button type="button" class="btn btn-block btn-default" data-toggle="modal" data-target="#modal-refreshtokendeleted-slack">Subscribe</button>
                                @else
                                    <a href="{{route('seatnotifications.delete.seat.notification', ['method' => 'slack', 'notification' => 'RefreshTokenDeleted'])}}" type="button" class="btn btn-block btn-danger">Unsubscribe</a>
                                @endif
                            </td>
                        </tr></tbody>
                    </table>
                </div>
                <!-- /.box -->
            </div>
        </div>

        <!-- RefreshTokenDeleted -->
        <div class="modal fade" id="modal-refreshtokendeleted-slack" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Slack Setting</h4>
                    </div>

                    <form method="post" action="{{route('seatnotifications.post.seat.notification')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="character_id" value="{{auth()->user()->id}}">
                        <input type="hidden" name="corporation_id" value="">
                        <input type="hidden" name="method" value="slack">
                        <input type="hidden" name="notification" value="RefreshTokenDeleted">

                        <div class="modal-body">
                            <p>You might add your personal SlackID (looks like this: U5KRYEK1V) or the Channel (f.e. #test) here, to which the Notification should be delivered to. This will be solved via oAuth Button in final. </p>
                            <label>Slack ID</label>
                            <input type="text" name="webhook" class="form-control" placeholder="Enter your SlackID f.e.U5KRYEK1V" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade" id="modal-refreshtokendeleted-discord" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Discord Setting</h4>
                    </div>

                    <form method="post" action="{{route('seatnotifications.post.seat.notification')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="character_id" value="{{auth()->user()->id}}">
                        <input type="hidden" name="corporation_id" value="">
                        <input type="hidden" name="method" value="discord">
                        <input type="hidden" name="notification" value="RefreshTokenDeleted">

                        <div class="modal-body">
                            <p> Here you can add the Discord Channels Webhook to receive notifications to this channel. In the final a method must be implemented where only one person per webhook can subscripe. Maybe even show which member set it up.</p>
                            <label>Discord Webhook</label>
                            <input type="text" name="webhook" class="form-control" placeholder="Enter your full Webhook URL" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Webhooks</h3>
                </div>

                <div class="box-body">
                    @if(!empty( setting("slack_webhook", true)))
                        <a href="{{route('seatnotifications.remove.slack.webhook')}}" type="button" class="btn btn-danger">Remove Slack Integration</a>
                    @else
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-slack">
                            Add Slack Webhook
                        </button>
                        @endif
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-slack" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Slack Webhook</h4>
                    </div>

                        <form method="post" action="{{route('seatnotifications.post.slack.webhook')}}">
                        {{csrf_field()}}

                        <div class="modal-body">
                            <label>Slack Webhook</label>
                            <input type="text" name="webhook" class="form-control" placeholder="{{$slack_webhook}}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>


@endsection