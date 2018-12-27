<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Models;

use Herpaderpaldent\Seat\SeatNotifications\Models\Discord\DiscordUser;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['channel_id', 'type', 'via'];

    /**
     * This returns the channel for the notification.
     *
     * @return mixed
     */
    public function routeNotificationForDiscord()
    {
        return $this->channel_id;
    }

    public function discord_user()
    {
        if($this->via === 'discord')
            return $this->hasOne(DiscordUser::class, 'channel_id', 'channel_id');

        return null;
    }

    public function group()
    {
        if($this->via === 'discord')
            return $this->discord_user->group;

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



}
