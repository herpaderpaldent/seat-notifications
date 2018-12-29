<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Models;

use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
use Herpaderpaldent\Seat\SeatNotifications\Models\Slack\SlackUser;
use Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshTokenDeletedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Seat\Eveapi\Models\RefreshToken;

class RefreshTokenNotification extends Model
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'herpaderp_refresh_token_notifications';

    protected $primaryKey = 'channel_id';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['channel_id', 'type', 'via'];

    public function discord_user()
    {
        if($this->via === 'discord')
            return $this->hasOne(DiscordUser::class, 'channel_id', 'channel_id');

        return null;
    }

    public function slack_user()
    {
        if($this->via === 'slack')
            return $this->belongsTo(SlackUser::class, 'channel_id', 'channel_id');

        return null;
    }

    public function group()
    {
        if($this->type === 'channel')
            return null;

        if($this->via === 'discord')
            return $this->discord_user->group;

        if($this->via === 'slack')
            return $this->slack_user->group;

        return null;
    }

    public function sendNotifiactions(RefreshToken $refresh_token)
    {
        $this->notify(new RefreshTokenDeletedNotification($refresh_token));
    }

    public function shouldReceive()
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

        if ($permissions->containsStrict($this->permission) || $permissions->containsStrict('superuser'))
            return true;

        return false;

    }

    public function recipient() : string
    {
        if(is_null($this->group()))
           return 'channel';

        $main_character = $this->group()->main_character;

        if (is_null($main_character)) {
            logger()->warning('Group has no main character set. Attempt to make assignation based on first attached character.', [
                'group_id' => $this->group()->id,
            ]);
            $main_character = $group->users->first()->character;
        }

        return $main_character;

    }



}
