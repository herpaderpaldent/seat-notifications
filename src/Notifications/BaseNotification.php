<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.12.2018
 * Time: 13:10
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Seat\Web\Models\Group;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * Assign this job a tag so that Horizon can categorize and allow
     * for specific tags to be monitored.
     *
     * If a job specifies the tags property, that is added.
     *
     * @return array
     */
    public function tags(): array
    {
        $tags = ['seatnotifications'];

        if (property_exists($this, 'tags'))
            return array_merge($this->tags, $tags);

        return $tags;
    }

    public function __construct()
    {
        $this->queue = 'high';
        $this->connection = 'redis';
    }

    public function getMainCharacter(Group $group)
    {
        $main_character = $group->main_character;

        if (is_null($main_character)) {
            logger()->warning('Group has no main character set. Attempt to make assignation based on first attached character.', [
                'group_id' => $group->id,
            ]);
            $main_character = $group->users->first()->character;
        }

        return $main_character;
    }

    abstract public function via($notifiable);

}