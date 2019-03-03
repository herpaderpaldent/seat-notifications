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
     * @var array
     */
    protected $tags = ['refresh_token'];

    /**
     * @var string
     */
    public $user_name;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $main_character;

    /**
     * @var string
     */
    public $corporation;

    /**
     * @var RefreshToken
     */
    public $refresh_token;

    /**
     * AbstractRefreshTokenNotification constructor.
     * @param RefreshToken $refresh_token
     */
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
     * @return array
     */
    final public static function getFilters(): ?string
    {
        return null;
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    abstract public function via($notifiable);
}
