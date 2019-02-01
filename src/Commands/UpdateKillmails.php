<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 18:26.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Commands;

use Herpaderpaldent\Seat\SeatNotifications\Models\SeatNotification;
use Illuminate\Console\Command;
use Seat\Eveapi\Jobs\Killmails\Character\Recent as CharacterRecent;
use Seat\Eveapi\Jobs\Killmails\Character\Detail as CharacterDetail;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Jobs\Killmails\Corporation\Detail as CorporationDetail;
use Seat\Eveapi\Jobs\Killmails\Corporation\Recent as CorporationRecent;
use Seat\Eveapi\Models\RefreshToken;

class UpdateKillmails extends Command
{

    protected $signature = 'seat-notifications:update:killmails';

    protected $description = 'This command dispatches character and corporation job to get new killmails.';

    public function handle()
    {
        $subscribed_corporations = SeatNotification::where('name', 'kill_mail')
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
                        new CharacterDetail($token)
                ])->dispatch($token)->onQueue('high');

                CorporationRecent::withChain([
                    new CorporationDetail($token),
                ])->dispatch($token)->onQueue('high');
            });

        $this->info('Processed ' . $tokens->count() . ' refresh tokens.');

    }
}
