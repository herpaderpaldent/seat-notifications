<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 21:08
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack;


use Herpaderpaldent\Seat\SeatNotifications\Http\Validation\ValidateSlackOAuth;
use Illuminate\Http\Request;

class SlackServerOAuthController
{
    /**
     * Scopes used in OAuth flow with Discord
     */
    const SCOPES = [
        'bot'
    ];

    public function postConfiguration(ValidateSlackOAuth $request)
    {
        $state = time();

        // store data into the session until OAuth confirmation
        session(['herpaderp.seatnotifications.discord.credentials' => [
            'state'         => $state,
            'client_id'     => $request->input('slack-configuration-client'),
            'client_secret' => $request->input('slack-configuration-secret'),
            'verification_token'     => $request->input('slack-configuration-verification'),
        ]]);

        return redirect($this->oAuthAuthorization($request->input('discord-configuration-client'), $state));
    }

    public function callback(Request $request)
    {

    }

    /**
     * Return an authorization uri with presets scopes
     *
     * @param $client_id
     * @param $state
     * @return string
     */
    private function oAuthAuthorization($client_id, $state)
    {
        $base_uri = 'https://slack.com/oauth/authorize?';

        return $base_uri . http_build_query([
                'client_id'     => $client_id,
                'scope'         => implode(' ', self::SCOPES),
                'state'         => $state,
                'redirect_uri'  => route('seatnotifications.callback.slack.server'),
            ]);
    }

}