<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 25.12.2018
 * Time: 10:54.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Validation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateOAuth extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'discord-configuration-client' => 'required|string',
            'discord-configuration-secret' => 'required|string',
            'discord-configuration-bot'    => 'required|string',
        ];
    }
}
