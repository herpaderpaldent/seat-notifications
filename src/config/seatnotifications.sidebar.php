<?php
/**
 * Created by PhpStorm.
 * User: Mutterschiff
 * Date: 11.02.2018
 * Time: 18:19
 */

return [
    'seatnotifications' => [
        'name'          => 'SeAT Notifications (POC)',
        'icon'          => 'fa-slack',
        'route_segment' => 'seatnotifications',
        'entries' => [
            [
                'name'  => 'SeAT Notifications',
                'icon'  => 'fa-slack',
                'route' => 'seatnotifications.index'
            ]
        ]
    ]
];