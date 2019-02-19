{{-- Check if the notification has been subscribed for the current user --}}
@if (!is_null($row::getDriver($provider)::getPrivateChannel()) && $row::isSubscribed($row::getDriver($provider)::getPrivateChannel()))
  <a href="" type="button" class="btn btn-app">
    <span class="badge bg-green">
      <i class="fa fa-check"></i>
    </span>
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </a>

{{-- Check if the current provider has been set --}}
@elseif ($row::getDriver($provider)::isSetup())
  <a href="{{ route('seatnotifications.notification.subscribe.private_channel', ['driver' => $provider, 'notification' => $row::getName(), 'client_id' => $row::getDriver($provider)::getPrivateChannel()]) }}" type="button" class="btn btn-app">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </a>

{{--Render a disabled button since none of the previous conditions has been met --}}
@else
  <button type="button" class="btn btn-app disabled">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>
@endif

