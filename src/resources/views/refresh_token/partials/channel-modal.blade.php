<div class="modal fade" id="{{ $provider_key }}-channel-refresh-token-deletion-modal" style="text-align: left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Subscribe to refresh token deletion ({{ $provider_label }})</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeToRefreshTokenDeletion{{ $provider_key }}" role="form"
              action="{{ route('seatnotifications.refresh_token.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="{{ $provider_key }}">

          <div class="form-group">
            <label for="available_channels">Select delivery channel:</label>
            <select name="channel_id" id="available_channels" class="form-control" style="width: 100%"
                    form="subscribeToRefreshTokenDeletion{{ $provider_key }}">
              <option></option>
              @foreach($available_channels as $channel)
                @if(!array_key_exists($provider_key, $channel))
                  @continue
                @endif

                @foreach($channel[$provider_key] as $channel)
                  <option value="{{ $channel['id'] }}">{{ $channel['name'] }}</option>
                @endforeach

              @endforeach
            </select>
          </div>

          <span class="help-block">
            Although you might chose the right channel here you need to make sure that the bot has the
            appropriate channel permission to post a message.
          </span>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="submit" form="subscribeToRefreshTokenDeletion{{ $provider_key }}" class="btn btn-primary">Save changes</button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>