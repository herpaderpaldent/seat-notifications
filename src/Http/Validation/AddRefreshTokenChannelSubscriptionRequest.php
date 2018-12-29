<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 11.07.2018
 * Time: 22:03.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Validation;

use Herpaderpaldent\Seat\SeatNotifications\Http\Controllers\Discord\DiscordServerController;
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
        /*if($this->input('via') === 'discord'){
            $channel_ids = array_keys((new DiscordServerController)->getChannels());

            return [
                'channel_id'=> [
                    'required',
                    'in_array:' . $channel_ids
                ],
                'via'=>'required'
            ];

        }*/

        return [
            'channel_id'=>'required',
            'via'=>'required',
        ];
    }
}
