@section('title', "Seat Notifications")
@section('page_header', "Overview Page")
@section('page_description', "Dashboard")

@extends('web::layouts.grids.12')

@section('content')
  <div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header">
            <i class="fa fa-comment"></i>
            <h3 class="box-title">Available Notifications</h3>
          </div>
          <div class="box-body">
            <table class="table compact table-condensed table-hover table-responsive" id="notifications_table">
              <thead>
                <tr>
                  <th>Notification</th>
                  <th>Personal</th>
                  <th>Public</th>
                </tr>
              </thead>
            </table>
          </div>
          {{-- /.box-body --}}
      </div>
      {{--  /.box --}}
    </div>
    {{-- /.col --}}
  </div>
  {{-- /.row --}}

  @include('seatnotifications::partials.channels_modal')
@endsection

@push('javascript')
  <script type="text/javascript">
    $('table#notifications_table').DataTable({
      processing  : true,
      serverSide  : true,
      ajax        : {
        url : '{{ route('seatnotifications.get.available.notification') }}',
      },
      columns     : [
        {data: 'notification'},
        {data: 'personal', className: 'text-center'},
        {data: 'public', className: 'text-center'},
      ]
    });

    $('#notifications-driver-channels')
      .on('show.bs.modal', function (event) {
        $(this).find('span[rel="driver"]').text(event.relatedTarget.dataset.driver);
        $(this).find('span[rel="notification"]').text(event.relatedTarget.dataset.title);
        $(this).find('input[name="driver"]').val(event.relatedTarget.dataset.driver);
        $(this).find('input[name="notification"]').val(event.relatedTarget.dataset.notification);

        var availableChannels = $('#available-channels');

        // request a list of available channels to the driver
        availableChannels.select2({
          width: '100%',
        });

        availableChannels.select2('data', null);

        $.ajax({
          type: 'GET',
          url: '{{ route('seatnotifications.driver.channels') }}',
          data: {
            'driver': event.relatedTarget.dataset.driver,
            'notification' : event.relatedTarget.dataset.notification
          },
        }).then(function (channels) {

          channels.forEach(function (channel) {

            var channel_name = channel.name;

            if (channel.private_channel === "true")
              channel_name += " (private)";

            // create the option and append to Select2
            var option = new Option(channel_name, channel.id, channel.subscribed, channel.subscribed);
            availableChannels.append(option).trigger('change');

            if (channel.subscribed) {

              // manually trigger the `select2:select` event
              availableChannels.trigger({
                type: 'select2:select',
                params: {
                  data: channel
                }
              });
            }
          });
        });

        // check provided filters
        event.relatedTarget.dataset.filters.split('|').forEach(function (filter) {

          if($.isEmptyObject(filter))
            return;

          var select = $('#' + filter + '-filter');

          // show the filter group
          select.parent('div').removeClass('hidden');

          // request a list of available channels to the driver
          select.select2({
            width: '100%',
            placeholder: "loading..."
          }).select2('data', null);

          //select.;

          $.ajax({
            type: 'GET',
            url : '{{ route('seatnotifications.notifications.filters') }}',
            data: {
              'filter'      : filter,
              'driver'      : event.relatedTarget.dataset.driver,
              'notification': event.relatedTarget.dataset.notification
            },
            complete: function () {
              select.select2({
                width: '100%',
              })
            }
          }).then(function (available_affiliations) {

            available_affiliations.forEach(function (available_affiliation) {

              // create the option and append to Select2
              var option = new Option(available_affiliation.name, available_affiliation.id, available_affiliation.subscribed, available_affiliation.subscribed);
              select.append(option).trigger('change');

              if (available_affiliation.subscribed) {

                // manually trigger the `select2:select` event
                select.trigger({
                  type: 'select2:select',
                  params: {
                    data: available_affiliation
                  }
                });
              }
            })
          })
        });
      })
      .on('hide.bs.modal', function (event) {
        // cleanup received channels
        $(this).find('option').remove().append('<option></option>');

        // hide all filters box from the modal on hide
        $(this).find('.channel-filter').addClass('hidden');

        // reset picked filters
        $(this).find('.channel-filter .select2').val([]).trigger('change');
      });
  </script>
@endpush