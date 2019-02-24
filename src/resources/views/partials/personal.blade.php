@foreach ($row::getDiversImplementations() as $provider => $implementation)
  @if($row::getDriver($provider)::allowPersonalNotifications())
    @if($implementation::hasPersonalNotification())
      @include('seatnotifications::partials.private_driver_button', compact('row', 'provider'))
    @endif
  @endif
@endforeach