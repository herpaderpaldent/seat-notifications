<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 09.03.2019
 * Time: 07:57
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Actions;


class AddSubscriptionStatus
{
    protected $get_public_driver_id;

    public function __construct(GetPublicDriverId $get_public_driver_id)
    {
        $this->get_public_driver_id = $get_public_driver_id;
    }

    public function execute(array $request, array $channels)
    {

        $public_driver_id = $this->get_public_driver_id->execute($request);

        if (empty($public_driver_id))
            return $channels;

        $data = collect($channels)->map(function ($channel) use ($public_driver_id) {

            return [
                'id'              => $channel['id'],
                'name'            => $channel['name'],
                'private_channel' => false,
                'subscribed'      => $channel['id'] === $public_driver_id,
            ];
        });

        return $data->toArray();
    }

}