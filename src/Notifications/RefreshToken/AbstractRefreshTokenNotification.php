<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken;

use Herpaderpaldent\Seat\SeatNotifications\Notifications\AbstractNotification;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;

/**
 * Class AbstractRefreshTokenNotification.
 * @package Herpaderpaldent\Seat\SeatNotifications\Notifications\RefreshToken
 */
abstract class AbstractRefreshTokenNotification extends AbstractNotification
{
    /**
     * @return string
     */
    final public static function getTitle(): string
    {
        return 'Refresh Token Deletion';
    }

    /**
     * @return string
     */
    final public static function getDescription(): string
    {
        return 'Receive a notification as soon a deletion is detected.';
    }

    /**
     * @return bool
     */
    final public static function isPublic(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    final public static function isPersonal(): bool
    {
        return true;
    }

    /**
     * @var array
     */
    protected $tags = ['refresh_token'];

    public $user_name;

    public $image;

    public $main_character;

    public $corporation;

    public $refresh_token;

    public function __construct(RefreshToken $refresh_token)
    {
        parent::__construct();

        $this->refresh_token = $refresh_token;
        $this->user_name = $refresh_token->user->name;
        $this->image = 'https://imageserver.eveonline.com/Character/' . $refresh_token->character_id . '_128.jpg';
        $this->main_character = $this->getMainCharacter($refresh_token->user->group)->name;
        $this->corporation = optional(CorporationInfo::find($refresh_token->user->character->corporation_id))->name ?: 'NPC Corporation';

        array_push($this->tags, 'user_name:' . $this->user_name);
    }

    abstract public function via($notifiable);
}
