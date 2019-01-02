<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 11.07.2018
 * Time: 22:03.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Validation;

use Illuminate\Foundation\Http\FormRequest;

class AddRefreshTokenChannelSubscriptionRequest extends FormRequest
{
    /**
     * Authorize the request by default.
     *
     * @return bool
     */
    public function authorize()
    {

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'channel_id'=>'required',
            'via'=>'required',
        ];
    }
}
