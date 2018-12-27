<a class="btn btn-app" href="{{--{{ route('seatnotifications.register.slack') }}--}}">
  @if(! is_null(setting('herpaderp.seatnotifications.slack.credentials.slack_id')))
    <span class="badge bg-green">registered</span>
  @endif
  <i class="fa fa-slack"></i> Slack
</a>