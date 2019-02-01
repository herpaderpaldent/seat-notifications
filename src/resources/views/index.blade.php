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

                    <h3 class="box-title">Register</h3>
                </div>
                <div class="box-body">
                    <p>Register to your prefered notification channel.</p>

                  @foreach($notification_channels as $channel)
                    @include($channel)
                  @endforeach

                </div>
                <!-- /.box -->
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-comment"></i>

                    <h3 class="box-title">Available Notifications</h3>
                </div>
                <div class="box-body">
                    <table class="table compact table-condensed table-hover table-responsive"
                           id="notifications_table">
                        <thead>
                        <tr>
                            <th>Notification</th>
                            <th>Private</th>
                            <th>Channel</th>
                        </tr>
                        </thead>
                    </table>

                </div>
                <!-- /.box -->
            </div>
        </div>

    </div>



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
          {data: 'private', className: 'text-center'},
          {data: 'channel', className: 'text-center'},
        ],
        drawCallback : function () {
          $(".select2").select2({
            placeholder: "{{ trans('web::seat.select_item_add') }}",
            allowClear: true
          });
        }
      });
    </script>

@endpush