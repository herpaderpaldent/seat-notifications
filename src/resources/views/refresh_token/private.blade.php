@inject('RefreshTokenController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController')

@if($RefreshTokenController->isAvailable())

  @foreach(config('services.seat-notification-channel') as $key => $provider)

    @if(! $provider::isSupportingPrivateNotifications())
      @continue
    @endif

    @if($RefreshTokenController->isDisabledButton('private', $key))
      <button type="button" class="btn btn-app disabled">
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </button>
    @elseif(! $RefreshTokenController->isSubscribed('private', $key))
      <a href="{{ route('seatnotifications.refresh_token.subscribe.user', ['via' => $key]) }}" type="button"
         class="btn btn-app">
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </a>
    @else
      <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.user', ['via' => $key]) }}" type="button"
         class="btn btn-app">
        <span class="badge bg-green">
          <i class="fa fa-check"></i>
        </span>
        <i class="fa {{ $provider::getButtonIconClass() }}"></i>{{ $provider::getButtonLabel() }}
      </a>
    @endif

  @endforeach

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif
