@inject('KillMailController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\KillMailController')

@if($KillMailController->isAvailable())

  @if( $KillMailController->isDisabledButton('private', 'discord'))
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @elseif(! $KillMailController->isSubscribed('private', 'discord'))
    <a href=" {{ route('seatnotifications.kill_mail.subscribe.user', ['via' => 'discord']) }}" type="button"
       class="btn btn-app">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @else
    <a href=" {{ route('seatnotifications.kill_mail.unsubscribe.user', ['via' => 'discord']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @endif

  @if( $KillMailController->isDisabledButton('private','slack'))
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-slack"></i>Slack
    </a>
  @elseif(! $KillMailController->isSubscribed('private', 'slack'))
    <a href=" {{ route('seatnotifications.kill_mail.subscribe.user', ['via' => 'slack']) }}" type="button"
       class="btn btn-app">
      <i class="fa fa-slack"></i>Slack
    </a>
  @else
    <a href=" {{ route('seatnotifications.kill_mail.unsubscribe.user', ['via' => 'slack']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-slack"></i>Slack
    </a>
  @endif

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif
