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

use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Seat\Eveapi\Models\Sde\InvType;

class DiscordKillMailNotification extends AbstractKillMailNotification
{
    const DANGER_COLOR = '14502713';

    const KILL_COLOR = '42586';

    public function via($notifiable)
    {

        array_push($this->tags, is_null($notifiable->group_id) ? 'to channel' : 'private to: ' . $this->getMainCharacter(Group::find($notifiable->group_id))->name);

        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {

        return (new DiscordMessage)
            ->embed(function ($embed) use ($notifiable) {

                $embed->title($this->getNotificationString())
                    ->thumbnail($this->image)
                    ->color($this->is_loss($notifiable) ? self::DANGER_COLOR : self::KILL_COLOR)
                    ->field('Value', $this->getValue($this->killmail_detail->killmail_id))
                    ->field('Involved Pilots', $this->getNumberOfAttackers(), true)
                    ->field('System', $this->getSystem(), true)
                    ->field('Link', $this->zKillBoardToLink('kill', $this->killmail_detail->killmail_id), true)
                    ->footer('zKillboard ' . $this->killmail_detail->killmail_time, 'https://zkillboard.com/img/wreck.png');
            });
    }

    private function getNotificationString(): string
    {

        return sprintf('%s just killed %s %s',
            $this->getAttacker(),
            $this->getVictim(),
            $this->getNumberOfAttackers() === 1 ? 'solo.' : ''
        );
    }

    private function getAttacker(): string
    {

        $killmail_attacker = $this->killmail_detail
            ->attackers
            ->where('final_blow', 1)
            ->first();

        return $this->getDiscordKMStringPartial(
            $killmail_attacker->character_id,
            $killmail_attacker->corporation_id,
            $killmail_attacker->ship_type_id,
            $killmail_attacker->alliance_id
        );
    }

    private function getDiscordKMStringPartial($character_id, $corporation_id, $ship_type_id, $alliance_id): string
    {

        $character = is_null($character_id) ? null : $this->resolveID($character_id);
        $corporation = is_null($corporation_id) ? null : $this->resolveID($corporation_id);
        $alliance = is_null($alliance_id) ? null : strtoupper('<' . $this->resolveID($alliance_id, true) . '>');
        $ship_type = optional(InvType::find($ship_type_id))->typeName;

        if (is_null($character_id))
            return sprintf('**%s** [%s] %s)',
                $ship_type,
                $corporation,
                $alliance
            );

        if (! is_null($character_id))
            return sprintf('**%s** [%s] %s flying a **%s**',
                $character,
                $corporation,
                $alliance,
                $ship_type
            );

        return '';
    }

    private function getVictim(): string
    {

        $killmail_victim = $this->killmail_detail->victims;

        return $this->getDiscordKMStringPartial(
            $killmail_victim->character_id,
            $killmail_victim->corporation_id,
            $killmail_victim->ship_type_id,
            $killmail_victim->alliance_id
        );
    }

    private function getSystem(): string
    {

        $solar_system = $this->killmail_detail->solar_system;

        return sprintf('[%s (%s)](%s)',
            $solar_system->itemName,
            number($solar_system->security, 2),
            $this->zKillBoardToLink('system', $solar_system->itemID)
        );

    }
}
