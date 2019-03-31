{{-- Check if the notification has been subscribed for the current provider --}}

@if (!is_null($row::getDriver($provider)::getPublicDriverId($row)) && $row::isSubscribed($row::getDriver($provider)::getPublicDriverId($row)))
  <button type="button" data-driver="{{ $provider }}" data-notification="{{ $row }}" data-title="{{ $row::getTitle() }}" data-filters="{{ $row::getFilters() }}" data-toggle="modal" data-target="#notifications-driver-channels" class="btn btn-app">
    <span class="badge bg-green">
      <i class="fa fa-check"></i>
    </span>
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>

@elseif ($row::getDriver($provider)::isSetup())
  <button type="button" data-driver="{{ $provider }}" data-notification="{{ $row }}" data-title="{{ $row::getTitle() }}" data-filters="{{ $row::getFilters() }}" data-toggle="modal" data-target="#notifications-driver-channels" class="btn btn-app">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>

{{-- Render a disabled button since none of the previous conditions has been met --}}

@else
  <button type="button" class="btn btn-app disabled">
    <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
  </button>
@endif