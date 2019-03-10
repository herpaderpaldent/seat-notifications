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
          width: '100%'
        });

        $.ajax({
          type: 'GET',
          url: '{{ route('seatnotifications.driver.channels') }}',
          data: {
            'driver': event.relatedTarget.dataset.driver,
            'notification' : event.relatedTarget.dataset.notification
          },
        }).then(function (channels) {

          channels.forEach(function (channel) {

            // create the option and append to Select2
            var option = new Option(channel.name, channel.id, channel.subscribed, channel.subscribed);
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
          var select = $('#' + filter + '-filter');

          // show the filter group
          select.parent('div').removeClass('hidden');

          // init the filter control
          select.select2({
            ajax: {
              url: '{{ route('seatnotifications.notifications.filters') }}',
              dataType: 'json',
              data: function (params) {
                return {
                  'filter': filter
                };
              },
              processResults: function (data, params) {
                return {
                  results: data,
                  pagination: {
                    more: false
                  }
                }
              }
            },
            templateResult: function (filter) {
              return filter.name;
            },
            templateSelection: function (filter) {
              return filter.name;
            },
            width: '100%'
          });
        });
      })
      .on('hide.bs.modal', function (event) {
        // hide all filters box from the modal on hide
        $(this).find('.channel-filter').addClass('hidden');

        // reset picked filters
        $(this).find('.channel-filter .select2').val([]).trigger('change');
      });
  </script>
@endpush