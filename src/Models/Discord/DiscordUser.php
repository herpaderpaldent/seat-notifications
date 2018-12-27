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
     * The table associated primary key.
     *
     * @var string
     */
    protected $primaryKey = 'group_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'discord_id', 'channel_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
