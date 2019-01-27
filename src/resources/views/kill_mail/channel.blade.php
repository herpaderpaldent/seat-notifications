@inject('KillMailController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\KillMailController')

@if($KillMailController->isAvailable())
  
  @if( $KillMailController->isDisabledButton('channel', 'discord') )
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @elseif(! $KillMailController->isSubscribed('channel', 'discord'))
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#discord-channel-killMail-modal">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @else
    <a href=" {{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => 'discord']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @endif

  @if( $KillMailController->isDisabledButton('channel', 'slack') )
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-slack"></i>Slack
    </a>
  @elseif(! $KillMailController->isSubscribed('channel', 'slack') )
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#slack-channel-killMail-modal">
      <i class="fa fa-slack"></i>Slack
    </a>
  @else
    <a href=" {{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => 'slack']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-slack"></i>Slack
    </a>
  @endif


  <div class="modal fade" id="discord-channel-killMail-modal" style="text-align: left">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
          <h4 class="modal-title">Subscribe to Kill Mails (Discord)</h4>
        </div>
        <div class="modal-body">

          <form id="subscribeTokillMailDiscord" role="form"
                action="{{ route('seatnotifications.kill_mail.subscribe.channel') }}" method="post">
            {{ csrf_field() }}

            <input type="hidden" name="via" value="discord">


            <div class="form-group">
              <label for="available_channels">Select delivery channel:</label>
              <select name="channel_id" id="available_channels" class="form-control" style="width: 100%"
                      form="subscribeTokillMailDiscord">
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
          <button type="submit" form="subscribeTokillMailDiscord" class="btn btn-primary">Save
            changes
          </button>
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

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
              <select name="channel_id" id="available_channels" class="form-control" style="width: 100%"
                      form="subscribeTokillMailSlack">
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
          <button type="submit" form="subscribeTokillMailSlack" class="btn btn-primary">Save changes</button>
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  @else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

