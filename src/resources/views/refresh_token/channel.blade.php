<div class="btn-group">
  @if(is_null(setting('herpaderp.seatnotifications.refresh_token.channel.discord', true)))
    <a href="" type="button" class="btn btn-app" data-toggle="modal" data-target="#discord-channel-modal">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @elseif (setting('herpaderp.seatnotifications.refresh_token.channel.discord', true) === 'unsubscribed')
    <a href="" type="button" class="btn btn-app" data-toggle="modal" data-target="#discord-channel-modal">
      <span class="badge bg-yellow"><i class="fa fa-close"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @else
    <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.channel', ['via' => 'discord']) }}" type="button" class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @endif
  {{--<button type="button" class="btn btn-default">Middle</button>
  <button type="button" class="btn btn-default">Right</button>--}}
</div>


{{--
<a href=" {{ route('seatnotifications.refresh_token.subscribe.channel', ['via' => 'discord']) }}" type="button" class="btn btn-app">
  <i class="fa fa-bullhorn"></i>Discord
</a>--}}

<div class="modal fade" id="discord-channel-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Discord channel selection</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeDiscordChannelForm" role="form"
              action="{{ route('seatnotifications.refresh_token.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="discord">

          <div class="row">
            <div class="form-group">
              <label for="available_channels">Select delivery channel:</label>
              <select name="channel_id" id="available_channels" class="form-control" style="width: 100%">
                @foreach($response as $channel_id => $channel_name)
                  <option value="{{ $channel_id }}">{{ $channel_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="submit" form="subscribeDiscordChannelForm" class="btn btn-primary">Save changes</button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>