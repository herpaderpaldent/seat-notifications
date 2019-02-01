<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 26.01.2019
 * Time: 09:18.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class SeatNotification extends Model
{
    /**
     * The table associated with the model.
     *herpaderp_seat_notification_notification_recipients.
     * @var string
     */
    protected $table = 'herpaderp_seat_notification_notification_recipients';

    public $incrementing = false;

    protected $fillable = ['channel_id', 'name', 'affiliation'];

    public function recipients()
    {
        return $this->belongsTo(SeatNotificationRecipient::class, 'channel_id', 'channel_id');
    }

    public function affiliations()
    {
        return json_decode($this->affiliation);
    }

    public function hasAffiliation(string $type, int $id) : bool
    {
        if($type === 'corp')
            return in_array($id, $this->affiliations()->corporations);

        return false;
    }
}
