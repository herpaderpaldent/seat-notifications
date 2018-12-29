<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 21:08.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack;

use Exception;
use GuzzleHttp\Client;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;

class SlackUserOAuthController
{
    /**
     * Scopes used in OAuth flow with slack.
     */
    const SCOPES = ['identity.basic'];

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Seat\Services\Exceptions\SettingException
     */
    public function join()
    {
        $state = time();

        $client_id = setting('herpaderp.seatnotifications.slack.credentials.client_id', true);

        if (is_null($client_id))
            return redirect()->route('seatnotifications.index')
                ->with('error', 'System administrator did not end the connector setup.');

        session(['herpaderp.seatnotifications.slack.user.state' => $state]);

        return redirect($this->oAuthAuthorization($client_id, $state));
    }

    public function callback()
    {
        $state = request()->session()->get('herpaderp.seatnotifications.slack.user.state');

        request()->session()->forget('herpaderp.seatnotifications.slack.user.state');

        if ($state != intval(request()->input('state')))
            return redirect()->route('seatnotifications.index')
                ->with('error', 'An error occurred while getting back the token. Returned state value is wrong. ' .
                    'In order to prevent any security issue, we stopped transaction.');

        try {
            $credentials = $this->exchangingToken(request()->input('code'));

            if (! in_array('identify', explode(',', $credentials['scope'])))
                return redirect()->route('seatnotifications.index')
                    ->with('error', 'We were not able to retrieve your user information. ' .
                        'Did you alter authorization ?');

            //$user_information = $this->retrievingUserInformation($credentials['access_token']);
            if(! $credentials['team']['id'] === setting('herpaderp.seatnotifications.slack.credentials.team_id'))
                return redirect()->route('seatnotifications.index')
                    ->with('error', 'The provided OAuth information do not match to the designates slack team.');

            $this->bindingUser($credentials);

        } catch (Exception $e) {
            return redirect()->route('seatnotifications.index')
                ->with('error', 'An error occurred while exchanging credentials with slack. ' . $e->getMessage());
        }

        return redirect()->route('seatnotifications.index')
            ->with('success', 'Now we know who to notify and you can subscribe to any of the available notifications.');

    }

    /**
     * Return an authorization uri with presets scopes.
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
                'redirect_uri'  => route('seatnotifications.callback.slack.user'),
            ]);
    }

    /**
     * Exchanging an authorization code to an access token.
     *
     * @param string $code
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Seat\Services\Exceptions\SettingException
     */
    private function exchangingToken(string $code) : array
    {
        $payload = [
            'client_id' => setting('herpaderp.seatnotifications.slack.credentials.client_id', true),
            'client_secret' => setting('herpaderp.seatnotifications.slack.credentials.client_secret', true),
            'code' => $code,
            'redirect_uri'  => route('seatnotifications.callback.slack.user'),
        ];

        $request = (new Client())->request('POST', 'https://slack.com/api/oauth.access', [
            'form_params' => $payload,
        ]);

        $response = json_decode($request->getBody(), true);

        if (is_null($response))
            throw new Exception('response from Slack was empty.');

        return array_merge($response, [
            'request_date' => array_first($request->getHeader('Date')),
        ]);
    }

    /**
     * Create a new SeAT/Discord user association.
     *
     * @param $credentials
     *
     * @throws \Seat\Services\Exceptions\SettingException
     */
    private function bindingUser($credentials)
    {
        $slack_id = $credentials['user']['id'];

        $channel_id = $this->getDmChannel($slack_id);

        // create a new binding between authenticated user and discord user.
        // in case a binding already exists, update credentials.
        SlackUser::updateOrCreate([
            'group_id'   => auth()->user()->group_id,
        ], [
            'slack_id'    => $slack_id,
            'channel_id'  => $channel_id,
        ]);

        setting(['herpaderp.seatnotifications.slack.credentials.slack_id', $slack_id]);
    }

    /**
     * @param $slack_id
     *
     * @return mixed
     */
    private function getDmChannel($slack_id)
    {
        return app('slack')
            ->imOpen([
                'user' => $slack_id,
            ])
            ->getChannel()
            ->getId();
    }
}
