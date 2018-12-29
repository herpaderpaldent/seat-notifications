<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 25.12.2018
 * Time: 10:54.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Validation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateSlackOAuth extends FormRequest
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
            'slack-configuration-client'       => 'required|string',
            'slack-configuration-secret'       => 'required|string',
            'slack-configuration-verification' => 'required|string',
        ];
    }
}
