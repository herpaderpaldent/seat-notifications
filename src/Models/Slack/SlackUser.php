<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Models\Slack;

use Illuminate\Database\Eloquent\Model;
use Seat\Web\Models\Group;

class SlackUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'herpaderp_slack_users';

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
    protected $fillable = ['group_id', 'slack_id', 'channel_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function isSlackUser(Group $group)
    {
        $slack_users = SlackUser::all()->filter(function ($slack_user) use ($group){
            return $slack_user->group->id === $group->id;
        });

        if($slack_users->isNotEmpty())
            return true;

        return false;
    }
}
