<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Slack;

use Exception;
use GuzzleHttp\Client;
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\SubscribeAction;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;

class SlackUserOAuthController
{
    /**
     * Scopes used in OAuth flow with slack.
     */
    const SCOPES = ['identity.basic'];

    protected $subscribe_action;

    public function __construct(SubscribeAction $subscribe_action)
    {
        $this->subscribe_action = $subscribe_action;
    }

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

        $driver = request()->session()->pull('herpaderp.seatnotifications.subscribe.driver');
        $notification = request()->session()->pull('herpaderp.seatnotifications.subscribe.notification');

        $data = [
            'driver' => $driver,
            'notification' => $notification,
            'driver_id' => SlackUser::find(auth()->user()->group->id)->channel_id,
            'group_id' => auth()->user()->group->id,
        ];

        return $this->subscribe_action->execute($data);
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
