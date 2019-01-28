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
    @include('seatnotifications::kill_mail.partials.discord-channel-modal', ['corporations' => $KillMailController->getAvailableCorporations()])
  @else
    <a href=" {{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => 'discord']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @endif

  @if( $KillMailController->isDisabledButton('channel', 'slack'))
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-slack"></i>Slack
    </a>
  @elseif(! $KillMailController->isSubscribed('channel', 'slack'))
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#slack-channel-killMail-modal">
      <i class="fa fa-slack"></i>Slack
    </a>
    @include('seatnotifications::kill_mail.partials.slack-channel-modal')
  @else
    <a href=" {{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => 'slack']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-slack"></i>Slack
    </a>
  @endif

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

