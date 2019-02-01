<div class="modal fade" id="slack-channel-refreshTokenDeletion-modal" style="text-align: left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Subscribe to refresh token deletion (Slack)</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeToRefreshTokenDeletionSlack" role="form"
              action="{{ route('seatnotifications.refresh_token.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="slack">


          <div class="form-group">
            <label for="available_channels">Select delivery channel:</label>
            <select name="channel_id" id="available_channels" class="form-control" style="width: 100%"
                    form="subscribeToRefreshTokenDeletionSlack">
              <option></option>
              @foreach($available_channels as $channel)
                @if(!array_key_exists('slack', $channel))
                  @continue
                @endif

                @foreach($channel['slack'] as $channel)
                  <option value="{{ $channel['id'] }}">
                    {{ $channel['name'] }}
                    @if($channel['private_channel'])
                      <i>(private channel)</i>
                    @endif
                  </option>
                @endforeach

              @endforeach
            </select>
          </div>

          <span class="help-block">If do not see the wished channel, invite the bot to it and try again later.</span>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="submit" form="subscribeToRefreshTokenDeletionSlack" class="btn btn-primary">Save changes</button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>