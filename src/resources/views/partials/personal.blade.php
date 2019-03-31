@foreach ($row::getDiversImplementations() as $provider => $implementation)
  <span style="float:left;">
  @if($row::getDriver($provider)::allowPersonalNotifications() && $implementation::hasPersonalNotification())
    @include('seatnotifications::partials.private_driver_button', compact('row', 'provider'))
  @endif
   </span>
@endforeach