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

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord;

use Exception;
use GuzzleHttp\Client;
use Herpaderpaldent\Seat\SeatNotifications\Caches\RedisRateLimitProvider;
use Herpaderpaldent\Seat\SeatNotifications\Http\Validations\ValidateOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use RestCord\DiscordClient;
use Seat\Web\Http\Controllers\Controller;
use WebSocket\Client as WebSocketClient;

class DiscordServerController extends Controller
{

    /**
     * Scopes used in OAuth flow with Discord.
     */
    const SCOPES = [
        'bot',
    ];

    /**
     * @var string
     */
    protected $gateway = 'wss://gateway.discord.gg';

    /**
     * @param ValidateOAuth $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postConfiguration(ValidateOAuth $request)
    {
        $state = time();

        // store data into the session until OAuth confirmation
        session(['herpaderp.seatnotifications.discord.credentials' => [
            'state'         => $state,
            'client_id'     => $request->input('discord-configuration-client'),
            'client_secret' => $request->input('discord-configuration-secret'),
            'bot_token'     => $request->input('discord-configuration-bot'),
        ]]);

        return redirect($this->oAuthAuthorization($request->input('discord-configuration-client'), $state));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callback(Request $request)
    {
        // get back pending OAuth credentials validation from session
        $credentials = $request->session()->get('herpaderp.seatnotifications.discord.credentials');

        $request->session()->forget('herpaderp.seatnotifications.discord.credentials');

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

        // validating Discord credentials
        try {

            $token = $this->exchangeToken($credentials['client_id'], $credentials['client_secret'],
                $request->input('code'));

            setting(['herpaderp.seatnotifications.discord.credentials.client_id', $credentials['client_id']], true);
            setting(['herpaderp.seatnotifications.discord.credentials.client_secret', $credentials['client_secret']], true);
            setting(['herpaderp.seatnotifications.discord.credentials.token', [
                'access'  => $token['access_token'],
                'refresh' => $token['refresh_token'],
                'expires' => carbon($token['request_date'])->addSeconds($token['expires_in'])->toDateTimeString(),
                'scope'  => $token['scope'],
            ]], true);
            setting(['herpaderp.seatnotifications.discord.credentials.bot_token', $credentials['bot_token']], true);
            setting(['herpaderp.seatnotifications.discord.credentials.guild_id', $request->input('guild_id')], true);

            // update Discord container
            app()->singleton('seatnotifications-discord', function () {
                return new DiscordClient([
                    'tokenType'         => 'Bot',
                    'token'             => setting('herpaderp.seatnotifications.discord.credentials.bot_token', true),
                    'rateLimitProvider' => new RedisRateLimitProvider(),
                ]);
            });

        } catch (Exception $e) {
            return redirect()->route('seatnotifications.configuration')
                ->with('error', 'An error occurred while trying to confirm OAuth credentials with Discord. ' .
                    $e->getMessage());
        }

        // Discord requires all bots to connect via a websocket connection and
        // identify at least once before any API requests over HTTP are allowed.
        // https://discordapp.com/developers/docs/topics/gateway#gateway-identify
        try {
            $this->gateway = $this->getGateway();

            $client = $this->getSocket($this->gateway);

            $client->send(json_encode([
                'op' => 2,
                'd' => [
                    'token' => setting('herpaderp.seatnotifications.discord.credentials.bot_token', true),
                    'v' => 3,
                    'compress' => false,
                    'properties' => [
                        '$os' => PHP_OS,
                        '$browser' => 'laravel-notification-channels-discord',
                        '$device' => 'laravel-notification-channels-discord',
                        '$referrer' => '',
                        '$referring_domain' => '',
                    ],
                ],
            ]));

            $response = $client->receive();
            $identified = Arr::get(json_decode($response, true), 'op') === 10;

            if (! $identified) {
                $this->error("Discord responded with an error while trying to identify the bot: $response");

                return redirect()->route('seatnotifications.configuration')
                    ->with('error', 'An error occurred while trying to create websocket connection with Discord. ' .
                        $identified);
            }

        } catch (Exception $e) {
            return redirect()->route('seatnotifications.configuration')
                ->with('error', 'An error occurred while trying to create websocket connection with Discord. ' .
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
        $base_uri = 'https://discordapp.com/api/oauth2/authorize?';

        return $base_uri . http_build_query([
                'response_type' => 'code',
                'client_id'     => $client_id,
                // https://discordapi.com/permissions.html#149504
                'permissions'   => 149504,
                'scope'         => implode(' ', self::SCOPES),
                'state'         => $state,
                'redirect_uri'  => route('seatnotifications.callback.discord.server'),
            ]);
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
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => route('seatnotifications.callback.discord.server'),
            'scope'         => implode(self::SCOPES, ' '),
        ];

        $request = (new Client())->request('POST', 'https://discordapp.com/api/oauth2/token', [
            'form_params' => $payload,
        ]);

        $response = json_decode($request->getBody(), true);

        if (is_null($response))
            throw new Exception('response from Discord was empty.');

        return array_merge($response, [
            'request_date' => array_first($request->getHeader('Date')),
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
        $expected_array_keys = ['state', 'client_id', 'client_secret', 'bot_token'];
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
     * Get the URL of the gateway that the socket should connect to.
     *
     * @return string
     */
    public function getGateway()
    {
        $gateway = $this->gateway;

        try {
            $response = (new Client)->get('https://discordapp.com/api/gateway', [
                'headers' => [
                    'Authorization' => 'Bot ' . setting('herpaderp.seatnotifications.discord.credentials.bot_token', true),
                ],
            ]);

            $gateway = Arr::get(json_decode($response->getBody(), true), 'url', $gateway);
        } catch (Exception $e) {

            return redirect()->route('seatnotifications.configuration')
                ->with('error', 'Could not get a websocket gateway address, defaulting to' . $gateway . '. An error occurred while trying to create websocket connection with Discord. ' .
                    $e->getMessage());
        }

        return $gateway;
    }

    /**
     * Get a websocket client for the given gateway.
     *
     * @param string $gateway
     *
     * @return \WebSocket\Client
     */
    public function getSocket($gateway)
    {
        return new WebSocketClient($gateway);
    }
}
