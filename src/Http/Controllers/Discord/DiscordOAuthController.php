<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.12.2018
 * Time: 12:40
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord;


use Exception;
use GuzzleHttp\Client;
use Herpaderpaldent\Seat\SeatNotifications\Caches\RedisRateLimitProvider;
use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use RestCord\DiscordClient;
use RestCord\Model\User\User;
use UnexpectedValueException;

class DiscordOAuthController
{
    const SCOPES = ['identify'];

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
            return redirect()->route('home')
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

        return redirect()->route('seatnotifications.index')
            ->with('success', 'Now we know who to notify and you can subscribe to any of the available notifications.');
    }

    /**
     * Getting a OAuth Authorization query with presets scopes
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
     * Exchanging an authorization code to an access token
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
     * Return information related user attached to the token
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
     * Create a new SeAT/Discord user association
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
        return app('discord')
            ->user
            ->createDm([
                'recipient_id' => $user->id
            ])
            ->id;
    }
    

}