<div class="modal fade" id="discord-channel-killMail-modal" style="text-align: left">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Subscribe to Kill Mails (Discord)</h4>
      </div>
      <div class="modal-body">

        <form id="subscribeTokillMailDiscord" role="form"
              action="{{ route('seatnotifications.kill_mail.subscribe.channel') }}" method="post">
          {{ csrf_field() }}

          <input type="hidden" name="via" value="discord">


          <div class="form-group">
            <label for="available_channels">Select delivery channel:</label>
            <select name="channel_id" id="available_channels" class="select2" style="width: 100%"
                    form="subscribeTokillMailDiscord">
              <option></option>
              @foreach($available_channels as $channel)
                @if(!array_key_exists('discord', $channel))
                  @continue
                @endif

                @foreach($channel['discord'] as $channel_id => $channel_name)
                  <option value="{{ $channel_id }}" @if($channel_id = $delivery_channel) selected @endif>{{ $channel_name }} </option>
                @endforeach

              @endforeach
            </select>
          </div>

          <span class="help-block">Although you might chose the right channel here you need to make sure that the bot has the appropriate channel permission to post a message.</span>

          <div class="row">
            <div class="col-md-12"></div>
            <div class="form-group-lg col-md-12">
              <label>Select your corporation.</label>
              <select class="select2" name="corporation_ids[]" style="width: 100%" multiple>

                @foreach($corporations as $corporation)
                  <option value="{{ $corporation['corporation_id'] }}" @if( $corporation['subscribed'] ) selected @endif>{{ $corporation['name'] }}</option>
                @endforeach

              </select>
            </div>
          </div>

        </form>


      </div>
      <div class="modal-footer">
        <a href="{{ route('seatnotifications.kill_mail.unsubscribe.channel', ['via' => 'discord']) }}" type="button" class="btn btn-danger pull-left">Remove Notification</a>
        <button type="submit" form="subscribeTokillMailDiscord" class="btn btn-primary">Save
          changes
        </button>
      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

@push('javascript')

  <script type="text/javascript">

      $("#discord-channel-killMail-modal").on('show.bs.modal', function () {
        console.log('test')
        $("#available_roles").select2({
          placeholder: "{{ trans('web::seat.select_item_add') }}"
        });
        $.ajax({
          type   : 'GET',
          url    : '{{ route('seatgroups.create') }}',
          success: function (data) {

            var select = $('#available_roles');

            select.empty();

            for (var i = 0; i < data.length; i++) {
              select.append($('<option></option>').attr('value', data[i].id).text(data[i].title));
            }
          },
          error  : function (xhr, textStatus, errorThrown) {
            console.log(xhr);
            console.log(textStatus);
            console.log(errorThrown);
          }
        })
      })


  </script>

@endpush