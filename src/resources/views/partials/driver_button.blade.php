<button type="button" class="btn btn-app disabled">
  <i class="fa {{ $row::getDriver($provider)::getButtonIconClass() }}"></i> {{ $row::getDriver($provider)::getButtonLabel() }}
</button>