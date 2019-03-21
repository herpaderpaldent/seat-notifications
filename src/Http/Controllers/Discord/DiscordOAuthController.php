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
use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\SubscribeAction;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use RestCord\DiscordClient;
use RestCord\Model\User\User;
use UnexpectedValueException;

class DiscordOAuthController
{
    /**
     * Scopes used in OAuth flow with discord.
     */
    const SCOPES = ['identify'];

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

        $client_id = setting('herpaderp.seatnotifications.discord.credentials.client_id', true);

        if (is_null($client_id))
            return redirect()->route('seatnotifications.index')
                ->with('error', 'System administrator did not end the connector setup.');

        session(['herpaderp.seatnotifications.discord.user.state' => $state]);

        return redirect($this->oAuthAuthorization($client_id, $state));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function callback()
    {
        $state = request()->session()->get('herpaderp.seatnotifications.discord.user.state');

        request()->session()->forget('herpaderp.seatnotifications.discord.user.state');

        if ($state != intval(request()->input('state')))
            return redirect()->route('seatnotifications.index')
                ->with('error', 'An error occurred while getting back the token. Returned state value is wrong. ' .
                    'In order to prevent any security issue, we stopped transaction.');

        try {
            $credentials = $this->exchangingToken(request()->input('code'));

            if (! in_array('identify', explode(' ', $credentials['scope'])))
                return redirect()->route('seatnotifications.index')
                    ->with('error', 'We were not able to retrieve your user information. ' .
                        'Did you alter authorization ?');

            $user_information = $this->retrievingUserInformation($credentials['access_token']);

            $this->bindingUser($user_information);

        } catch (Exception $e) {
            return redirect()->route('seatnotifications.index')
                ->with('error', 'An error occurred while exchanging credentials with Discord. ' . $e->getMessage());
        }

        $driver = request()->session()->pull('herpaderp.seatnotifications.subscribe.driver');
        $notification = request()->session()->pull('herpaderp.seatnotifications.subscribe.notification');

        $data = [
            'driver' => $driver,
            'notification' => $notification,
            'driver_id' => DiscordUser::find(auth()->user()->group->id)->channel_id,
            'group_id' => auth()->user()->group->id,
        ];

        return $this->subscribe_action->execute($data);
    }

    /**
     * Getting a OAuth Authorization query with presets scopes.
     *
     * @param string $client_id
     * @param int $state
     * @return string
     */
    private function oAuthAuthorization(string $client_id, int $state)
    {
        $base_uri = 'https://discordapp.com/api/oauth2/authorize?';

        return $base_uri . http_build_query([
                'client_id'     => $client_id,
                'response_type' => 'code',
                'state'         => $state,
                'redirect_uri'  => route('seatnotifications.callback.discord.user'),
                'scope'         => implode(' ', self::SCOPES),
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
            'client_id' => setting('herpaderp.seatnotifications.discord.credentials.client_id', true),
            'client_secret' => setting('herpaderp.seatnotifications.discord.credentials.client_secret', true),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri'  => route('seatnotifications.callback.discord.user'),
            'scope' => implode(' ', self::SCOPES),
        ];

        $request = (new Client())->request('POST', 'https://discordapp.com/api/oauth2/token', [
            'form_params' => $payload,
        ]);

        $response = json_decode($request->getBody(), true);

        if (is_null($response))
            throw new UnexpectedValueException('response from Discord was empty.');

        return array_merge($response, [
            'expires_at' => carbon(array_first($request->getHeader('Date')))->addSeconds($response['expires_in']),
        ]);
    }

    /**
     * Return information related user attached to the token.
     *
     * @param string $access_token
     *
     * @return \RestCord\Model\User\User
     */
    private function retrievingUserInformation(string $access_token)
    {
        $driver = new DiscordClient([
            'token' => $access_token,
            'tokenType' => 'OAuth',
            'rateLimitProvider' => new RedisRateLimitProvider(),
        ]);

        return $driver->user->getCurrentUser([]);
    }

    /**
     * Create a new SeAT/Discord user association.
     *
     * @param User $user
     *
     * @throws \Seat\Services\Exceptions\SettingException
     */
    private function bindingUser(User $user)
    {
        $channel_id = $this->getDmChannel($user);
        // create a new binding between authenticated user and discord user.
        // in case a binding already exists, update credentials.
        DiscordUser::updateOrCreate([
            'group_id'   => auth()->user()->group_id,
        ], [
            'discord_id'    => $user->id,
            'channel_id'    => $channel_id,
        ]);

        setting(['herpaderp.seatnotifications.discord.credentials.discord_id', $user->id]);

    }

    /**
     * @param \RestCord\Model\User\User $user
     *
     * @return mixed
     */
    private function getDmChannel(User $user)
    {
        return app('seatnotifications-discord')
            ->user
            ->createDm([
                'recipient_id' => $user->id,
            ])
            ->id;
    }
}
