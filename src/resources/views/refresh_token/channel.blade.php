@inject('RefreshTokenController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\RefreshTokenController')

@if($RefreshTokenController->isAvailable())

  @foreach(config('services.seat-notification-channel') as $key => $provider)

    @if( $RefreshTokenController->isDisabledButton('channel', $key) )
      <button type="button" class="btn btn-app disabled">
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </button>
    @elseif(! $RefreshTokenController->isSubscribed('channel', $key))
      <a href="" type="button" class="btn btn-app" data-toggle="modal"
         data-target="#{{ $key }}-channel-refreshTokenDeletion-modal">
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </a>

      @include('seatnotifications::refresh_token.partials.channel-modal', [
        'provider_key'   => $key,
        'provider_label' => $provider::getButtonLabel(),
      ])

    @else
      <a href=" {{ route('seatnotifications.refresh_token.unsubscribe.channel', ['via' => $key]) }}" type="button"
         class="btn btn-app">
        <span class="badge bg-green"><i class="fa fa-check"></i></span>
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </a>
    @endif

  @endforeach

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

