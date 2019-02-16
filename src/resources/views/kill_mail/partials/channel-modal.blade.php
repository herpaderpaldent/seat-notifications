<div class="modal fade" id="{{ $provider_key }}-channel-kill-mail-modal" style="text-align: left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Subscribe to Kill Mails ({{ $provider_label }})</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeToKillMail{{ $provider_key }}" role="form"
              action="{{ route('seatnotifications.kill_mail.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="{{ $provider_key }}">

          <div class="form-group">
            <label for="available_channels">Select delivery channel:</label>
            <select name="channel_id" id="available_channels" class="select2" style="width: 100%"
                    form="subscribeToKillMail{{ $provider_key }}">
              <option></option>
              @foreach($available_channels as $channel)
                @if(! array_key_exists($provider_key, $channel))
                  @continue
                @endif

                @foreach($channel[$provider_key] as $channel)
                  <option value="{{ $channel['id'] }}" @if($channel['id'] == $delivery_channel) selected @endif>{{ $channel['name'] }}</option>
                @endforeach

              @endforeach
            </select>
          </div>

          <span class="help-block">
            Although you might chose the right channel here you need to make sure that the bot has the
            appropriate channel permission to post a message.
          </span>

          <div class="row">
            <div class="col-md-12"></div>
            <div class="form-group-lg col-md-12">
              <label for="corporation_ids">Select your corporation.</label>
              <select class="select2" name="corporation_ids[]" id="corporation_ids" style="width: 100%" multiple>

                @foreach($corporations as $corporation)
                  <option value="{{ $corporation['corporation_id'] }}" @if( $corporation['subscribed'] ) selected @endif>{{ $corporation['name'] }}</option>
                @endforeach

              </select>
            </div>
          </div>

        </form>

      </div>
      <div class="modal-footer">
        <a href="{{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => $provider_key]) }}" type="button" class="btn btn-danger pull-left">Remove Notification</a>
        <button type="submit" form="subscribeToKillMail{{ $provider_key }}" class="btn btn-primary">Save changes</button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
