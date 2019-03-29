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

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications\KillMail;

use Herpaderpaldent\Seat\SeatNotifications\Notifications\AbstractNotification;
use Seat\Eveapi\Models\Killmails\KillmailDetail;
use Seat\Eveapi\Models\Killmails\KillmailVictimItem;
use Seat\Eveapi\Models\Market\Price;

abstract class AbstractKillMailNotification extends AbstractNotification
{
    /**
     * @var string
     */
    public $killmail_detail;

    /**
     * @var string
     */
    public $image;

    /**
     * AbstractKillMailNotification constructor.
     */
    public function __construct(int $killmail_id)
    {

        parent::__construct();

        $this->killmail_detail = KillmailDetail::find($killmail_id);
        $this->image = sprintf('https://imageserver.eveonline.com/Type/%d_64.png',
            $this->killmail_detail->victims->ship_type_id);

        array_push($this->tags, 'killmail_id:' . $killmail_id);
    }

    /**
     * @return string
     */
    final public static function getTitle(): string
    {

        return 'Kill Mail Notification';
    }

    /**
     * @return string
     */
    final public static function getDescription(): string
    {

        return 'Receive a notification about new kill mails.';
    }

    /**
     * @return bool
     */
    final public static function isPublic(): bool
    {

        return true;
    }

    /**
     * @return bool
     */
    final public static function isPersonal(): bool
    {

        return false;
    }

    /**
     * @return array
     */
    final public static function getFilters(): ?string
    {

        return 'corporations';
    }

    /**
     * Determine the permission needed to represent driver buttons.
     * @return string
     */
    public static function getPermission(): string
    {
        return 'seatnotifications.kill_mail';
    }

    /**
     * @param $notifiable
     *
     * @return mixed
     */
    abstract public function via($notifiable);

    public function resolveID($id, $is_alliance = false)
    {

        $cached_entry = cache('name_id:' . $id);

        if (! is_null($cached_entry))
            return $cached_entry;

        if ($is_alliance)
            return $this->getAllianceTicker($id);

        // Resolve the Esi client library from the IoC
        $eseye = app('esi-client')->get();
        $eseye->setBody([$id]);
        $names = $eseye->invoke('post', '/universe/names/');

        $name = collect($names)->first()->name;

        cache(['name_id:' . $id => $name], carbon()->addCentury());

        return $name;
    }

    public function getNumberOfAttackers(): int
    {

        return $this->killmail_detail->attackers->count();
    }

    public function is_loss($notifiable): bool
    {

        return $notifiable
            ->subscriptions
            ->firstwhere('notification', AbstractKillMailNotification::class)
            ->hasAffiliation('corporation', $this->killmail_detail->victims->corporation_id);
    }

    public function getValue(int $killmail_id): string
    {

        $value = KillmailVictimItem::where('killmail_id', $killmail_id)
            ->get()
            ->map(function ($item) {

                return Price::find($item->item_type_id);
            })
            ->push(Price::find($this->killmail_detail->victims->ship_type_id))
            ->sum('average_price');

        return number($value) . ' ISK';
    }

    /**
     * Build a link to zKillboard using Slack message formatting.
     *
     * @param string $type (must be ship, character, corporation or alliance)
     * @param int    $id   the type entity ID
     * @param string $name the type name
     *
     * @return string
     */
    public function zKillBoardToLink(string $type, int $id)
    {

        if (! in_array($type, ['ship', 'character', 'corporation', 'alliance', 'kill', 'system']))
            return '';

        return sprintf('https://zkillboard.com/%s/%d/', $type, $id);
    }

    private function getAllianceTicker($id)
    {

        $cached_entry = cache('alliance_ticker:' . $id);

        if (! is_null($cached_entry))
            return $cached_entry;

        // Resolve the Esi client library from the IoC
        $eseye = app('esi-client')->get();
        $ticker = $eseye->invoke('get', '/alliances/' . $id)->ticker;

        cache(['alliance_ticker:' . $id => $ticker], carbon()->addCentury());

        return $ticker;
    }
}
