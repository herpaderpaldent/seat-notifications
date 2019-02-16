@inject('killMailController', 'Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Notifications\KillMailController')

@if($killMailController->isAvailable())

  @foreach(config('services.seat-notification-channel') as $key => $provider)

    @if ($killMailController->isDisabledButton('channel', $key))
      <button type="button" class="btn btn-app disabled">
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </button>
    @elseif(! $killMailController->isSubscribed('channel', $key))
      <button type="button" class="btn btn-app" data-toggle="modal" data-target="#{{ $key }}-channel-kill-mail-modal">
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </button>

      @include('seatnotifications::kill_mail.partials.channel-modal', [
        'corporations' => $killMailController->getAvailableCorporations('channel', $key),
        'delivery_channel' => 0,
        'provider_key' => $key,
        'provider_label' => $provider::getButtonLabel(),
      ])

    @else
      <button type="button" class="btn btn-app" data-toggle="modal" data-target="#{{ $key }}-channel-kill-mail-modal">
        <span class="badge bg-green">
          <i class="fa fa-check"></i>
        </span>
        <i class="fa {{ $provider::getButtonIconClass() }}"></i> {{ $provider::getButtonLabel() }}
      </button>

      @include('seatnotifications::kill_mail.partials.channel-modal', [
        'corporations' => $killMailController->getAvailableCorporations('channel', $key),
        'delivery_channel' => $killMailController->getChannelChannelId($key, 'kill_mail'),
        'provider_key' => $key,
        'provider_label' => $provider::getButtonLabel(),
      ])

    @endif

  @endforeach

@else

  @include('seatnotifications::seatnotifications.partials.missing-permissions')

@endif

