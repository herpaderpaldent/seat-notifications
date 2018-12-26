<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Models\Discord;

use Illuminate\Database\Eloquent\Model;
use Seat\Web\Models\Group;

class DiscordUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'herpaderp_discord_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'discord_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
