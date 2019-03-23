<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
     * Determine the permission needed to represent driver buttons.
     * @return string
     */
    public static function getPermission(): string
    {
        return 'seatnotifications.refresh_token';
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    abstract public function via($notifiable);
}
