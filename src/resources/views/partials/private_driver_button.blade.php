{{-- Check if the notification has been subscribed for the current user --}}

@if (!is_null($row::getDriver($provider)::getPrivateChannel()) && $row::isSubscribed($row::getDriver($provider)::getPrivateChannel()))

  <form id="{{ $provider }}-private-unsubscribe-from-{{ class_basename($row) }}" action="{{ route('seatnotifications.notification.unsubscribe.channel') }}" method="post">
    {{ csrf_field() }}

    <input type="hidden" name="driver" value="{{ $provider }}" />
    <input type="hidden" name="notification" value="{{ $row }}" />
    <input type="hidden" name="driver_id" value="{{ $row::getDriver($provider)::getPrivateChannel() }}" />
  </form>

  <button type="submit" form="{{ $provider }}-private-unsubscribe-from-{{ class_basename($row) }}" type="button" class="btn btn-app">
      <span class="badge bg-green">
      <i class="fa fa-check"></i>
    </span>
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>

@elseif ($row::getDriver($provider)::isSetup())
  <form id="{{ $provider }}-private-subscribe-to-{{ class_basename($row) }}" action="{{ route('seatnotifications.notification.subscribe.channel') }}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="driver" value="{{ $provider }}" />
    <input type="hidden" name="notification" value="{{ $row }}" />
    <input type="hidden" name="driver_id" value="{{ $row::getDriver($provider)::getPrivateChannel() }}" />
    <input type="hidden" name="group_id" value="{{ auth()->user()->group->id }}" />
  </form>

  <button type="submit" form="{{ $provider }}-private-subscribe-to-{{ class_basename($row) }}" type="button" class="btn btn-app">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>

{{--Render a disabled button since none of the previous conditions has been met --}}

@else
  <button type="button" class="btn btn-app disabled">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>
@endif