<?php
/**
 * MIT License
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
 *
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Models;

use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SeatNotificationRecipient extends Model
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'herpaderp_seat_notification_recipients';

    public $primaryKey = 'channel_id';

    public $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['channel_id', 'notification_channel', 'is_channel'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Eloquent\Relations\HasOne|null
     */
    public function notification_user()
    {

        if($this->notification_channel === 'discord')
            return $this->belongsTo(DiscordUser::class, 'channel_id', 'channel_id');

        if($this->notification_channel === 'slack')
            return $this->belongsTo(SlackUser::class, 'channel_id', 'channel_id');

        return null;
    }

    public function notifications()
    {
        return $this->hasMany(SeatNotification::class, 'channel_id', 'channel_id');
    }

    /**
     * Returns a boolean if a certain Recipient should receive a given notification.
     *
     * @param string $notification Name of the notification that should be checked.
     *
     * @return bool
     */
    public function shouldReceive(string $notification, array $ids = null) : bool
    {

        return $this->notifications
            ->filter(function ($seat_notification) use ($notification) {

                return $seat_notification->name === $notification;
            })
            ->filter(function ($seat_notification) use ($ids) {

                if($ids === null)
                    return true;

                if($seat_notification->affiliation === null)
                    return true;

                foreach ($ids as $id) {

                    if(empty($id))
                        return false;

                    if($seat_notification->hasAffiliation('corp', $id))
                        return true;
                }

                return false;
            })
            ->isNotEmpty();
    }

    /**
     * @return string
     */
    public function recipient() : string
    {
        if(is_null($this->notification_user))
            return 'channel';

        $main_character = $this->notification_user->group->main_character->name;

        if (is_null($main_character)) {
            logger()->warning('Group has no main character set. Attempt to make assignation based on first attached character.', [
                'group_id' => $this->notification_user->group->id,
            ]);
            $main_character = $this->notification_user->group->users->first()->character->name;
        }

        return $main_character;

    }
}
