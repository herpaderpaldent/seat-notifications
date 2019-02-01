<div class="modal fade" id="slack-channel-killMail-modal" style="text-align: left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Subscribe to Kill Mails (Slack)</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeTokillMailSlack" role="form"
              action="{{ route('seatnotifications.kill_mail.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="slack">


          <div class="form-group">
            <label for="available_channels">Select delivery channel:</label>
            <select name="channel_id" id="available_channels" class="select2" style="width: 100%"
                    form="subscribeTokillMailSlack">
              <option></option>
              @foreach($available_channels as $channel)
                @if(!array_key_exists('slack', $channel))
                  @continue
                @endif

                @foreach($channel['slack'] as $channel)
                  <option value="{{ $channel['id'] }}" @if($channel['id'] = $delivery_channel) selected @endif> {{ $channel['name'] }}
                    @if($channel['private_channel'])
                      <i>(private channel)</i>
                    @endif
                  </option>
                @endforeach

              @endforeach
            </select>
          </div>

          <span class="help-block">If do not see the wished channel, invite the bot to it and try again later.</span>

          <div class="row">
            <div class="col-md-12"></div>
            <div class="form-group-lg col-md-12">
              <label>{{trans('seatgroups::seat.seat_groups_role')}}</label>
              <select class="select2" name="corporation_ids[]" style="width: 100%" multiple>

                @foreach($corporations as $corporation)
                  <option value="{{ $corporation['corporation_id'] }}" @if( $corporation['subscribed'] ) selected @endif>{{ $corporation['name'] }}</option>
                @endforeach

              </select>
            </div>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <a href="{{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => 'slack']) }}" type="button" class="btn btn-danger pull-left">Remove Notification</a>
        <button type="submit" form="subscribeTokillMailSlack" class="btn btn-primary">Save changes</button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>