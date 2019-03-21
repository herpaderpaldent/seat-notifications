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

use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage;
use Seat\Eveapi\Models\Sde\InvType;

class SlackKillMailNotification extends AbstractKillMailNotification
{
    const LOSS_COLOR = '#DD4B39';

    const KILL_COLOR = '#00A65A';

    public function via($notifiable)
    {

        array_push($this->tags, is_null($notifiable->group_id) ? 'to channel' : 'private to: ' . $this->getMainCharacter(Group::find($notifiable->group_id))->name);

        return [SlackChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage
     */
    public function toSlack($notifiable)
    {

        return (new SlackMessage)
            ->attachment(function ($attachment) use ($notifiable) {

                $attachment->content($this->getNotificationString())
                    ->thumb($this->image)
                    ->fields([
                        'Value'           => $this->getValue($this->killmail_detail->killmail_id),
                        'Involved Pilots' => $this->getNumberOfAttackers(),
                        'System'          => $this->getSystem(),
                        'Link'            => $this->zKillBoardToLink('kill', $this->killmail_detail->killmail_id),
                    ])
                    ->markdown(['System'])
                    ->color($this->is_loss($notifiable) ? self::LOSS_COLOR : self::KILL_COLOR)
                    ->footerIcon('https://zkillboard.com/img/wreck.png')
                    ->footer('zKillboard ' . $this->killmail_detail->killmail_time);
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

        return $this->getSlackKMStringPartial(
            $killmail_attacker->character_id,
            $killmail_attacker->corporation_id,
            $killmail_attacker->ship_type_id,
            $killmail_attacker->alliance_id
        );
    }

    /**
     * @param int $killmail_id
     *
     * @return string
     */
    private function getVictim(): string
    {

        $killmail_victim = $this->killmail_detail->victims;

        return $this->getSlackKMStringPartial(
            $killmail_victim->character_id,
            $killmail_victim->corporation_id,
            $killmail_victim->ship_type_id,
            $killmail_victim->alliance_id
        );
    }

    private function getSlackKMStringPartial($character_id, $corporation_id, $ship_type_id, $alliance_id): string
    {

        $character = is_null($character_id) ? null : $this->resolveID($character_id);
        $corporation = is_null($corporation_id) ? null : $this->resolveID($corporation_id);
        $alliance = is_null($alliance_id) ? null : strtoupper('<' . $this->resolveID($alliance_id, true) . '>');
        $ship_type = optional(InvType::find($ship_type_id))->typeName;

        if (is_null($character_id))
            return sprintf('*%s* [%s] %s)',
                $ship_type,
                $corporation,
                $alliance
            );

        if (! is_null($character_id))
            return sprintf('*%s* [%s] %s flying a *%s*',
                $character,
                $corporation,
                $alliance,
                $ship_type
            );

        return '';
    }

    private function getSystem(): string
    {

        $solar_system = $this->killmail_detail->solar_system;

        return sprintf('<%s|%s (%s)>',
            $this->zKillBoardToLink('system', $solar_system->itemID),
            $solar_system->itemName,
            number($solar_system->security, 2)
        );

    }
}
