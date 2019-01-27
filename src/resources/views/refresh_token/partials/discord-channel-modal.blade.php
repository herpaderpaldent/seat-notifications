<div class="modal fade" id="discord-channel-refreshTokenDeletion-modal" style="text-align: left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Subscribe to refresh token deletion (Discord)</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeToRefreshTokenDeletionDiscord" role="form"
              action="{{ route('seatnotifications.refresh_token.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="discord">


          <div class="form-group">
            <label for="available_channels">Select delivery channel:</label>
            <select name="channel_id" id="available_channels" class="form-control" style="width: 100%"
                    form="subscribeToRefreshTokenDeletionDiscord">
              <option></option>
              @foreach($available_channels as $channel)
                @if(!array_key_exists('discord', $channel))
                  @continue
                @endif

                @foreach($channel['discord'] as $channel_id => $channel_name)
                  <option value="{{ $channel_id }}">{{ $channel_name }}</option>
                @endforeach

              @endforeach
            </select>
          </div>

          <span class="help-block">Although you might chose the right channel here you need to make sure that the bot has the appropriate channel permission to post a message.</span>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="submit" form="subscribeToRefreshTokenDeletionDiscord" class="btn btn-primary">Save
          changes
        </button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>