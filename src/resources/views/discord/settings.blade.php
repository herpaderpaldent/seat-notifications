<div class=" col-md-4">
  <div class="box box-default">
    <div class="box-header with-border">
      <i class="fa fa-wechat"></i>

      <h3 class="box-title">Discord</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <form role="form" action="{{ route('herpaderp.seatnotifications.discord.post.configuration') }}" method="post"
            class="form-horizontal">
        {{ csrf_field() }}

        <div class="box-body">

          <legend>Discord API</legend>

          @if (! is_null(setting('herpaderp.seatnotifications.discord.credentials.client_id', true)))
            <p class="callout callout-warning text-justify">It appears you already have a Discord API access setup.
              In order to prevent any mistakes, <code>Client ID</code> and <code>Client Secret</code> fields have been
              disabled.
              Please use the rubber in order to enable modifications.</p>
          @endif

          <div class="form-group">
            <label for="discord-configuration-client" class="col-md-4">Discord Client ID</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.discord.credentials.client_id', true) == null)
                  <input type="text" class="form-control" id="discord-configuration-client"
                         name="discord-configuration-client"/>
                @else
                  <input type="text" class="form-control " id="discord-configuration-client"
                         name="discord-configuration-client"
                         value="{{ setting('herpaderp.seatnotifications.discord.credentials.client_id', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                  <button type="button" class="btn btn-danger btn-flat" id="discord-client-eraser">
                      <i class="fa fa-eraser"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="discord-configuration-secret" class="col-md-4">Discord Client Secret</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.discord.credentials.client_secret', true) == null)
                  <input type="text" class="form-control" id="discord-configuration-secret"
                         name="discord-configuration-secret"/>
                @else
                  <input type="text" class="form-control" id="discord-configuration-secret"
                         name="discord-configuration-secret"
                         value="{{ setting('herpaderp.seatnotifications.discord.credentials.client_secret', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                                    <button type="button" class="btn btn-danger btn-flat" id="discord-secret-eraser">
                                        <i class="fa fa-eraser"></i>
                                    </button>
                                </span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="discord-configuration-bot" class="col-md-4">Discord Bot Token</label>
            <div class="col-md-7">
              <div class="input-group input-group-sm">
                @if (setting('herpaderp.seatnotifications.discord.credentials.bot_token', true) == null)
                  <input type="text" class="form-control" id="discord-configuration-bot"
                         name="discord-configuration-bot"/>
                @else
                  <input type="text" class="form-control" id="discord-configuration-bot"
                         name="discord-configuration-bot"
                         value="{{ setting('herpaderp.seatnotifications.discord.credentials.bot_token', true) }}" readonly/>
                @endif
                <span class="input-group-btn">
                                    <button type="button" class="btn btn-danger btn-flat" id="discord-bot-eraser">
                                        <i class="fa fa-eraser"></i>
                                    </button>
                                </span>
              </div>
              <span class="help-block text-justify">
                                In order to generate credentials, please go on <a
                        href="https://discordapp.com/developers/applications/me" target="_blank">your Discord apps</a> and create a new app.
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
    $('#discord-client-eraser').on('click', function(){
      var discord_client = $('#discord-configuration-client');
      discord_client.val('');
      discord_client.removeAttr("readonly");
    });
    $('#discord-secret-eraser').on('click', function(){
      var discord_secret = $('#discord-configuration-secret');
      discord_secret.val('');
      discord_secret.removeAttr("readonly");
    });
    $('#discord-bot-eraser').on('click', function(){
      var discord_secret = $('#discord-configuration-bot');
      discord_secret.val('');
      discord_secret.removeAttr("readonly");
    });
    $('[data-toggle="tooltip"]').tooltip();
  </script>
@endpush