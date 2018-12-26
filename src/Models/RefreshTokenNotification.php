<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshTokenNotification extends Model
{
    /**
     * Name of this Notification.
     *
     * @var string
     */
    public $name = "Refresh Token Notification";

    /**
     * Description of the Model's notification.
     *
     * @var string
     */
    public $description = "This Notification will alert you if someones refresh_token is removed.";

    /**
     * Notification channels supported by this Model.
     *
     * @var array
     */
    public $channels = ['Discord'];

    /**
     * Needed permission to receive this Model's notification.
     *
     * @var string
     */
    public $permission = "refresh_token";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'herpaderp_refresh_token_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['channel_id', 'type', 'via'];

}
