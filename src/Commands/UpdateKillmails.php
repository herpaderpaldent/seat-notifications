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

namespace Herpaderpaldent\Seat\SeatNotifications\Commands;

use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationSubscription;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMail\AbstractKillMailNotification;
use Illuminate\Console\Command;
use Seat\Eveapi\Jobs\Killmails\Character\Detail as CharacterDetail;
use Seat\Eveapi\Jobs\Killmails\Character\Recent as CharacterRecent;
use Seat\Eveapi\Jobs\Killmails\Corporation\Detail as CorporationDetail;
use Seat\Eveapi\Jobs\Killmails\Corporation\Recent as CorporationRecent;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\RefreshToken;

class UpdateKillmails extends Command
{

    protected $signature = 'seat-notifications:update:killmails';

    protected $description = 'This command dispatches character and corporation job to get new killmails.';

    public function handle()
    {
        $subscribed_corporations = NotificationSubscription::where('notification', AbstractKillMailNotification::class)
            ->get()
            ->map(function ($seat_notification) {
                return $seat_notification->affiliations()->corporations;
            })
            ->flatten()
            ->toArray();

        $tokens = RefreshToken::all()
            ->filter(function ($refresh_token) use ($subscribed_corporations) {
                return in_array(CharacterInfo::find($refresh_token->character_id)->corporation_id, $subscribed_corporations);
            })
            ->each(function ($token) {

                // Killmails
                CharacterRecent::withChain([
                        new CharacterDetail($token),
                ])->dispatch($token)->onQueue('high');

                CorporationRecent::withChain([
                    new CorporationDetail($token),
                ])->dispatch($token)->onQueue('high');
            });

        $this->info('Processed ' . $tokens->count() . ' refresh tokens.');

    }
}
