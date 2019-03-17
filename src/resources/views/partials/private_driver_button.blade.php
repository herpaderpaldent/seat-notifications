{{-- Check if the notification has been subscribed for the current user --}}

@if (!is_null($row::getDriver($provider)::getPrivateChannel()) && $row::isSubscribed($row::getDriver($provider)::getPrivateChannel()))
  <a href="{{ route('seatnotifications.notification.unsubscribe.channel', ['notification' => $row, 'driver' => $provider, 'driver_id' => $row::getDriver($provider)::getPrivateChannel()]) }}" type="button" class="btn btn-app">
    <span class="badge bg-green">
      <i class="fa fa-check"></i>
    </span>
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </a>

{{-- Check if the current provider has been set --}}

@elseif ($row::getDriver($provider)::isSetup())
  <form id="{{ $provider }}-private-subscribe-to-notification" action="{{ route('seatnotifications.notification.subscribe.channel') }}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="driver" value="{{ $provider }}" />
    <input type="hidden" name="notification" value="{{ $row }}" />
    <input type="hidden" name="driver_id" value="{{ $row::getDriver($provider)::getPrivateChannel() }}" />

    <button type="submit" form="{{ $provider }}-private-subscribe-to-notification" type="button" class="btn btn-app">
      <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
    </button>
  </form>

{{--Render a disabled button since none of the previous conditions has been met --}}

@else
  <button type="button" class="btn btn-app disabled">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>
@endif
