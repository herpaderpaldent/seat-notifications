<?php

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
        $user = $this->belongsTo(DiscordUser::class, 'channel_id', 'channel_id');

        if(is_null($user))
            $user = $this->belongsTo(SlackUser::class, 'channel_id', 'channel_id');

        return $user;
    }

    public function notifications()
    {
        return $this->hasMany(SeatNotification::class, 'channel_id', 'channel_id');
    }

    /**
     * @return null|\Seat\Web\Models\Group
     */
    /*public function group() : ?Group
    {
        if($this->type === 'channel')
            return null;

        if($this->type === 'private')
            return $this->user->group;

        return null;
    }*/

    /**
     * @return bool
     */
    /*public function shouldReceive()
    {
        if($this->type === 'channel')
            return true;

        $permissions = collect();

        if(! is_null($this->group())) {
            foreach ($this->group()->roles as $role) {
                foreach ($role->permissions as $permission){
                    $permissions->push($permission->title);
                }
            }
        }

        if ($permissions->containsStrict($this->notification) || $permissions->containsStrict('superuser'))
            return true;

        return false;
    }*/

    /**
     * @return string
     */
    /*public function recipient() : string
    {
        if(is_null($this->group()))
            return 'channel';

        $main_character = $this->group()->main_character->name;

        if (is_null($main_character)) {
            logger()->warning('Group has no main character set. Attempt to make assignation based on first attached character.', [
                'group_id' => $this->group()->id,
            ]);
            $main_character = $this->group()->users->first()->character->name;
        }

        return $main_character;

    }*/




}
