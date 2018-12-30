<div class=" col-md-4">
  <div class="box box-default">
    <div class="box-header with-border">
      <i class="fa fa-slack"></i>

      <h3 class="box-title">Slack</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <form role="form" action="{{ route('herpaderp.seatnotifications.slack.post.configuration') }}" method="post"
            class="form-horizontal">
        {{ csrf_field() }}

        <div class="box-body">

          <legend>Slack API</legend>

          @if (! is_null(setting('herpaderp.seatnotifications.slack.credentials.client_id', true)))
            <p class="callout callout-warning text-justify">It appears you already have a Slack API access setup.
              In order to prevent any mistakes, <code>Client ID</code> and <code>Client Secret</code> fields have been
              disabled.
              Please use the rubber in order to enable modifications.</p>
          @endif

          <div class="form-group">
            <label for="slack-configuration-client" class="col-md-4">Slack Client ID</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.slack.credentials.client_id', true) == null)
                  <input type="text" class="form-control" id="slack-configuration-client"
                         name="slack-configuration-client"/>
                @else
                  <input type="text" class="form-control " id="slack-configuration-client"
                         name="slack-configuration-client"
                         value="{{ setting('herpaderp.seatnotifications.slack.credentials.client_id', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                  <button type="button" class="btn btn-danger btn-flat" id="slack-client-eraser">
                      <i class="fa fa-eraser"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="slack-configuration-secret" class="col-md-4">Slack Client Secret</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.slack.credentials.client_secret', true) == null)
                  <input type="text" class="form-control" id="slack-configuration-secret"
                         name="slack-configuration-secret"/>
                @else
                  <input type="text" class="form-control" id="slack-configuration-secret"
                         name="slack-configuration-secret"
                         value="{{ setting('herpaderp.seatnotifications.slack.credentials.client_secret', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                    <button type="button" class="btn btn-danger btn-flat" id="slack-secret-eraser">
                        <i class="fa fa-eraser"></i>
                    </button>
                </span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="slack-configuration-verification" class="col-md-4">Slack Verification Token</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.slack.credentials.verification_token', true) == null)
                  <input type="text" class="form-control" id="slack-configuration-verification"
                         name="slack-configuration-verification"/>
                @else
                  <input type="text" class="form-control" id="slack-configuration-verification"
                         name="slack-configuration-verification"
                         value="{{ setting('herpaderp.seatnotifications.slack.credentials.verification_token', true) }}" readonly/>
                @endif
                  <span class="input-group-btn">
                      <button type="button" class="btn btn-danger btn-flat" id="slack-verification-eraser">
                          <i class="fa fa-eraser"></i>
                      </button>
                  </span>
              </div>
              <span class="help-block">
                In order to generate credentials, please go on <a href="https://api.slack.com/apps" target="_blank">your slack apps</a> and create a new application.
              </span>
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
    $('#slack-client-eraser').on('click', function(){
      var slack_client = $('#slack-configuration-client');
      slack_client.val('');
      slack_client.removeAttr("readonly");
    });
    $('#slack-secret-eraser').on('click', function(){
      var slack_secret = $('#slack-configuration-secret');
      slack_secret.val('');
      slack_secret.removeAttr("readonly");
    });
    $('#slack-verification-eraser').on('click', function(){
      var slack_verification = $('#slack-configuration-verification');
      slack_verification.val('');
      slack_verification.removeAttr("readonly");
    });
    $('[data-toggle="tooltip"]').tooltip();
  </script>
@endpush