<div class="modal fade" id="notifications-driver-channels">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Subscribe to <span rel="notification"></span> (<span rel="driver"></span>)</h4>
      </div>
      {{-- /.modal-header --}}
      <div class="modal-body">
        <form id="subscribe-to-notification" role="form" action="{{ route('seatnotifications.notification.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="driver" value="" />
          <input type="hidden" name="notification" value="" />
          <input type="hidden" name="public" value="true" />

          <div class="form-group">
            <label for="available-channels">Select delivery channel:</label>
            <select name="driver_id" id="available-channels" class="select2">
              <option></option>
            </select>
            <span class="help-block">
              Although you might chose the right channel here, you need to make sure that the driver has the
              appropriate permission to post a message.
            </span>
          </div>
          {{-- /.form-group --}}

          <div class="form-group hidden channel-filter">
            <label for="corporations-filter">Select corporation filter:</label>
            <select name="corporations_filter[]" id="corporations-filter" multiple="multiple" class="select2">
            </select>
            <span class="help-block">
              Please specify the list of corporations for which you want receive notification.
              If you're leaving this field empty, you'll receive notification for all corporations.
            </span>
          </div>
          {{-- /.form-group --}}

          <div class="form-group hidden channel-filter">
            <label for="characters-filter">Select character filter:</label>
            <select name="characters_filter[]" id="characters-filter" multiple="multiple" class="select2">
              <option></option>
            </select>
            <span class="help-block">
              Please specify the list of characters for which you want receive notification.
              If you're leaving this field empty, you'll receive notification for all characters.
            </span>
          </div>
          {{-- ./form-group --}}

        </form>
        {{-- /form --}}
      </div>
      {{-- /.modal-body --}}
      <div class="modal-footer">

        <form id="unsubscribe-from-notification" role="form" action="{{ route('seatnotifications.notification.unsubscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="driver" value="" />
          <input type="hidden" name="notification" value="" />

        </form>
        {{-- /form --}}
        <button type="submit" form="unsubscribe-from-notification" class="btn btn-danger pull-left">Unsubscribe</button>
        <button type="submit" form="subscribe-to-notification" class="btn btn-primary">Subscribe</button>
      </div>
      {{-- /.modal-footer --}}
    </div>
  </div>
</div>