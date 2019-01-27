@inject('RefreshTokenController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController')

@if($RefreshTokenController->isAvailable())

  @if( $RefreshTokenController->isDisabledButton('channel', 'discord') )
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @elseif(! $RefreshTokenController->isSubscribed('channel', 'discord'))
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#discord-channel-refreshTokenDeletion-modal">
      <i class="fa fa-bullhorn"></i>Discord
    </a>
    @include('seatnotifications::refresh_token.partials.discord-channel-modal')
  @else
    <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.channel', ['via' => 'discord']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-bullhorn"></i>Discord
    </a>
  @endif

  @if( $RefreshTokenController->isDisabledButton('channel', 'slack'))
    <a href="" type="button" class="btn btn-app disabled">
      <i class="fa fa-slack"></i>Slack
    </a>
  @elseif(! $RefreshTokenController->isSubscribed('channel', 'slack'))
    <a href="" type="button" class="btn btn-app" data-toggle="modal"
       data-target="#slack-channel-refreshTokenDeletion-modal">
      <i class="fa fa-slack"></i>Slack
    </a>
    @include('seatnotifications::refresh_token.partials.slack-channel-modal')
  @else
    <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.channel', ['via' => 'slack']) }}" type="button"
       class="btn btn-app">
      <span class="badge bg-green"><i class="fa fa-check"></i></span>
      <i class="fa fa-slack"></i>Slack
    </a>
  @endif

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

