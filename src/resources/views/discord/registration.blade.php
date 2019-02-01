@inject('DiscordUser', 'Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser' )

@if(! is_null(setting('herpaderp.seatnotifications.discord.credentials.bot_token', true)))
  <a class="btn btn-app" href="{{ route('seatnotifications.register.discord') }}">
    @if( $DiscordUser::find(auth()->user()->group->id) )
      <span class="badge bg-green">registered</span>
    @endif
    <i class="fa fa-bullhorn"></i> Discord
  </a>
@endif