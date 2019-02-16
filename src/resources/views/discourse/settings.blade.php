<div class=" col-md-4">
  <div class="box box-default">
    <div class="box-header with-border">
      <i class="fa fa-comments"></i>

      <h3 class="box-title">Discourse</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <form role="form" action="{{ route('herpaderp.seatnotifications.discourse.post.configuration') }}" method="post"
            class="form-horizontal">
        {{ csrf_field() }}

        <div class="box-body">

          <legend>Discord API</legend>

          @if (! is_null(setting('herpaderp.seatnotifications.discourse.credentials.api_key', true)))
            <p class="callout callout-warning text-justify">It appears you already have a Discourse API access setup.
              In order to prevent any mistakes, <code>API Key</code> and <code>Forum URL</code> fields have been disabled.
              Please use the rubber in order to enable modifications.</p>
          @endif

          <div class="form-group">
            <label for="discourse-configuration-client" class="col-md-4">Discourse API Key</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.discourse.credentials.api_key', true) == null)
                  <input type="text" class="form-control" id="discourse-configuration-key"
                         name="discourse-configuration-key"/>
                @else
                  <input type="text" class="form-control " id="discourse-configuration-key"
                         name="discourse-configuration-key"
                         value="{{ setting('herpaderp.seatnotifications.discourse.credentials.api_key', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                  <button type="button" class="btn btn-danger btn-flat" id="discourse-api-eraser">
                    <i class="fa fa-eraser"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="discourse-configuration-url" class="col-md-4">Discourse Forum URL</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.discourse.credentials.url', true) == null)
                  <input type="text" class="form-control" id="discourse-configuration-url"
                         name="discourse-configuration-url"/>
                @else
                  <input type="text" class="form-control" id="discourse-configuration-url"
                         name="discourse-configuration-url"
                         value="{{ setting('herpaderp.seatnotifications.discourse.credentials.url', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                  <button type="button" class="btn btn-danger btn-flat" id="discourse-url-eraser">
                    <i class="fa fa-eraser"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>

        </div>

        <div class="box-footer">
          <button type="submit" class="btn btn-primary pull-right">Update</button>
        </div>

      </form>

    </div>
    <!-- /.box-body -->
  </div>
</div>

@push('javascript')
  <script type="application/javascript">
      $('#discourse-api-eraser').on('click', function(){
          var discord_client = $('#discourse-configuration-key');
          discord_client.val('');
          discord_client.removeAttr("readonly");
      });
      $('#discourse-url-eraser').on('click', function(){
          var discord_secret = $('#discourse-configuration-url');
          discord_secret.val('');
          discord_secret.removeAttr("readonly");
      });
      $('[data-toggle="tooltip"]').tooltip();
  </script>
@endpush