@if ($row::getDriver($provider)::isSetup())
  <button type="button" data-name="{{ $provider }}" data-notification="{{ $row }}" data-toggle="modal" data-target="#notifications-driver-channels" class="btn btn-app">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>
@else
  <button type="button" class="btn btn-app disabled">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>
@endif