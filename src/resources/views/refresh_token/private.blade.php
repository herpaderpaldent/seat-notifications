@inject('RefreshTokenController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController')

@if(! $RefreshTokenController->isSubscribed(auth()->user()->group, 'discord'))
  <a href=" {{ route('seatnotifications.refresh_token.subscribe.user', ['via' => 'discord']) }}" type="button"
     class="btn btn-app">
    <i class="fa fa-bullhorn"></i>Discord
  </a>
@else
  <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.user', ['via' => 'discord']) }}" type="button"
     class="btn btn-app">
    <span class="badge bg-green"><i class="fa fa-check"></i></span>
    <i class="fa fa-bullhorn"></i>Discord
  </a>
@endif

@if(! $RefreshTokenController->isSubscribed(auth()->user()->group, 'slack'))
  <a href=" {{ route('seatnotifications.refresh_token.subscribe.user', ['via' => 'slack']) }}" type="button"
     class="btn btn-app">
    <i class="fa fa-slack"></i>Slack
  </a>
@else
  <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.user', ['via' => 'slack']) }}" type="button"
     class="btn btn-app">
    <span class="badge bg-green"><i class="fa fa-check"></i></span>
    <i class="fa fa-slack"></i>Slack
  </a>
@endif

