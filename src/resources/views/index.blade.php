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

    $('#notifications-driver-channels').on('show.bs.modal', function (event) {
        $(this).find('span[rel="driver"]').text(event.relatedTarget.dataset.driver);
        $(this).find('span[rel="notification"]').text(event.relatedTarget.dataset.title);
        $(this).find('input[name="driver"]').val(event.relatedTarget.dataset.driver);
        $(this).find('input[name="notification"]').val(event.relatedTarget.dataset.notification);

        $('#available-channels').select2({
            ajax: {
                url: '{{ route('seatnotifications.driver.channels') }}',
                dataType: 'json',
                data: function (params) {
                    return {
                        'driver': event.relatedTarget.dataset.driver
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
            templateResult: function (channel) {
                return channel.name;
            },
            templateSelection: function (channel) {
                return channel.name;
            },
            width: '100%'
        });
    });
  </script>
@endpush