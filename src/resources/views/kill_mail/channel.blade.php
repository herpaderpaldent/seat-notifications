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
    @include('seatnotifications::kill_mail.partials.discord-channel-modal', ['corporations' => $KillMailController->getAvailableCorporations('channel', 'discord'), 'delivery_channel' => null])
  @else
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#discord-channel-killMail-modal">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
    @include('seatnotifications::kill_mail.partials.discord-channel-modal', ['corporations' => $KillMailController->getAvailableCorporations('channel', 'discord'), 'delivery_channel' => $KillMailController->getChannelChannelId('discord', 'kill_mail')])
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
    @include('seatnotifications::kill_mail.partials.slack-channel-modal', ['corporations' => $KillMailController->getAvailableCorporations('channel', 'slack'), 'delivery_channel' => null])
    {{--@include('seatnotifications::kill_mail.partials.slack-channel-modal', ['corporations' => $KillMailController->getAvailableCorporations('channel', 'slack')])--}}
  @else
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#slack-channel-killMail-modal">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-slack"></i>Slack
    </a>
    @include('seatnotifications::kill_mail.partials.slack-channel-modal', ['corporations' => $KillMailController->getAvailableCorporations('channel', 'slack'), 'delivery_channel' => $KillMailController->getChannelChannelId('slack', 'kill_mail')])
  @endif

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

