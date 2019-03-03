@foreach ($row::getDriversImplementations() as $provider => $implementation)
  @if($row::getDriver($provider)::allowPersonalNotifications() && $implementation::isPersonal())
    @include('seatnotifications::partials.private_driver_button', compact('row', 'provider'))
  @endif
@endforeach