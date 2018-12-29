@if(is_null(setting('herpaderp.seatnotifications.refresh_token.status.discord')))
  <a href=" {{ route('seatnotifications.refresh_token.subscribe.user', ['via' => 'discord']) }}" type="button"
     class="btn btn-app">
    <i class="fa fa-bullhorn"></i>Discord
  </a>
@elseif (setting('herpaderp.seatnotifications.refresh_token.status.discord') === 'unsubscribed')
  <a href=" {{ route('seatnotifications.refresh_token.subscribe.user', ['via' => 'discord']) }}" type="button"
     class="btn btn-app">
    <span class="badge bg-yellow"><i class="fa fa-close"></i></span>
    <i class="fa fa-bullhorn"></i>Discord
  </a>
@else
  <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.user', ['via' => 'discord']) }}" type="button"
     class="btn btn-app">
    <span class="badge bg-green"><i class="fa fa-check"></i></span>
    <i class="fa fa-bullhorn"></i>Discord
  </a>
@endif

@if(is_null(setting('herpaderp.seatnotifications.slack.credentials.slack_id')))
  <a href=" {{ route('seatnotifications.refresh_token.subscribe.user', ['via' => 'slack']) }}" type="button"
     class="btn btn-app">
    <i class="fa fa-slack"></i>Slack
  </a>
@elseif (setting('herpaderp.seatnotifications.refresh_token.status.slack') === 'unsubscribed')
  <a href=" {{ route('seatnotifications.refresh_token.subscribe.user', ['via' => 'slack']) }}" type="button"
     class="btn btn-app">
    <span class="badge bg-yellow"><i class="fa fa-close"></i></span>
    <i class="fa fa-slack"></i>Slack
  </a>
@else
  <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.user', ['via' => 'slack']) }}" type="button"
     class="btn btn-app">
    <span class="badge bg-green"><i class="fa fa-check"></i></span>
    <i class="fa fa-slack"></i>Slack
  </a>
@endif
{{--<button type="button" class="btn btn-default">Middle</button>
<button type="button" class="btn btn-default">Right</button>--}}

