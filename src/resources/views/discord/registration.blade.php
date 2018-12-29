<a class="btn btn-app" href="{{ route('seatnotifications.register.discord') }}">
  @if(! is_null(setting('herpaderp.seatnotifications.discord.credentials.discord_id')))
    <span class="badge bg-green">registered</span>
  @endif
  <i class="fa fa-bullhorn"></i> Discord
</a>