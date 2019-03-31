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
use Herpaderpaldent\Seat\SeatNotifications\Http\Validations\ValidateSlackOAuth;
use Illuminate\Http\Request;
use JoliCode\Slack\ClientFactory;

class SlackServerOAuthController
{
    /**
     * Scopes used in OAuth flow with slack.
     */
    const SCOPES = [
        'bot',
    ];

    public function postConfiguration(ValidateSlackOAuth $request)
    {
        $state = time();

        // store data into the session until OAuth confirmation
        session(['herpaderp.seatnotifications.slack.credentials' => [
            'state'         => $state,
            'client_id'     => $request->input('slack-configuration-client'),
            'client_secret' => $request->input('slack-configuration-secret'),
            'verification_token'     => $request->input('slack-configuration-verification'),
        ]]);

        return redirect($this->oAuthAuthorization($request->input('slack-configuration-client'), $state));
    }

    public function callback(Request $request)
    {
        // get back pending OAuth credentials validation from session
        $credentials = $request->session()->get('herpaderp.seatnotifications.slack.credentials');

        $request->session()->forget('herpaderp.seatnotifications.slack.credentials');

        if (! $this->isValidCallback($credentials))
            return redirect()->route('home')
                ->with('error', 'An error occurred while processing the request. ' .
                    'For some reason, your session was not met system requirement.');

        // ensure request is legitimate
        if ($credentials['state'] != $request->input('state')) {
            return redirect()->back()
                ->with('error', 'An error occurred while getting back the token. Returned state value is wrong. ' .
                    'In order to prevent any security issue, we stopped transaction.');
        }

        // validating Slack credentials
        try {

            $token = $this->exchangeToken($credentials['client_id'], $credentials['client_secret'],
                $request->input('code'));

            setting(['herpaderp.seatnotifications.slack.credentials.client_id', $credentials['client_id']], true);
            setting(['herpaderp.seatnotifications.slack.credentials.client_secret', $credentials['client_secret']], true);
            setting(['herpaderp.seatnotifications.slack.credentials.token', [
                'access'  => $token['access_token'],
                'scope'  => $token['scope'],
                'request_date' => $token['request_date'],
            ]], true);
            setting(['herpaderp.seatnotifications.slack.credentials.bot_access_token', $token['bot']['bot_access_token']], true);
            setting(['herpaderp.seatnotifications.slack.credentials.verification_token', $credentials['verification_token']], true);
            setting(['herpaderp.seatnotifications.slack.credentials.team_id', $token['team_id']], true);

            // update Slack container
            app()->singleton('slack', function () {
                return (new ClientFactory)->create(setting('herpaderp.seatnotifications.slack.credentials.bot_access_token', true));
            });

        } catch (Exception $e) {
            return redirect()->route('seatnotifications.configuration')
                ->with('error', 'An error occurred while trying to confirm OAuth credentials with slack. ' .
                    $e->getMessage());
        }

        return redirect()->route('seatnotifications.configuration')
            ->with('success', 'The bot credentials has been set.');

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
                'redirect_uri'  => route('seatnotifications.callback.slack.server'),
            ]);
    }

    /**
     * Ensure an array is containing all expected values in a valid callback session.
     *
     * @param $session_content
     * @return bool
     */
    private function isValidCallback($session_content)
    {
        $expected_array_keys = ['state', 'client_id', 'client_secret', 'verification_token'];
        $i = count($expected_array_keys);

        if (is_null($session_content))
            return false;

        if (! is_array($session_content))
            return false;

        while ($i > 0) {
            $i--;

            if (! array_key_exists($expected_array_keys[$i], $session_content))
                return false;
        }

        return true;
    }

    /**
     * Exchange an Authorization Code with an Access Token.
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $code
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function exchangeToken(string $client_id, string $client_secret, string $code)
    {
        $payload = [
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'code'          => $code,
            'redirect_uri'  => route('seatnotifications.callback.slack.server'),
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
}
