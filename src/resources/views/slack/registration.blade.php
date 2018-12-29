@inject('SlackUser', 'Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser' )

<a class="btn btn-app" href="{{ route('seatnotifications.register.slack') }}">
  @if($SlackUser->isSlackUser(auth()->user()->group))
    <span class="badge bg-green">registered</span>
  @endif
  <i class="fa fa-slack"></i> Slack
</a>

