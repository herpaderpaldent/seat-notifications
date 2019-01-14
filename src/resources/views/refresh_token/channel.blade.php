@inject('RefreshTokenController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController')

@if(auth()->user()->has('seatnotifications.view', false) && auth()->user()->has('seatnotifications.configuration', false) && auth()->user()->has('seatnotifications.refresh_token', false))

  @if( $RefreshTokenController->isDisabledButton('discord','channel') )
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @elseif(! $RefreshTokenController->isSubscribed(auth()->user()->group, 'discord', true))
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#discord-channel-refreshTokenDeletion-modal">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @else
    <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.channel', ['via' => 'discord']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @endif

  @if( $RefreshTokenController->isDisabledButton('slack','channel') )
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-slack"></i>Slack
    </a>
  @elseif(! $RefreshTokenController->isSubscribed(auth()->user()->group, 'slack', true) )
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#slack-channel-refreshTokenDeletion-modal">
      <i class="fa fa-slack"></i>Slack
    </a>
  @else
    <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.channel', ['via' => 'slack']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-slack"></i>Slack
    </a>
  @endif


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
          <button type="submit" form="subscribeToRefreshTokenDeletionDiscordDiscord" class="btn btn-primary">Save
            changes
          </button>
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

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

  @else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

