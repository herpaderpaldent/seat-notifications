@foreach ($row::getDiversImplementations() as $provider => $implementation)
  @if($row::getDriver($provider)::allowPublicNotifications())
    @include('seatnotifications::partials.driver_button', compact('row', 'provider'))
  @endif
@endforeach