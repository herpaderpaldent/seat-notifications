<?php
/**
 * Created by PhpStorm.
 * User: Mutterschiff
 * Date: 11.02.2018
 * Time: 18:19.
 */

return [
    'seatnotifications' => [
        'name'          => 'SeAT Notifications (POC)',
        'icon'          => 'fa-inbox',
        'route_segment' => 'seatnotifications',
        'entries' => [
            [
                'name'  => 'Notifications',
                'icon'  => 'fa-envelope',
                'route' => 'seatnotifications.index',
            ],
            [
                'name'  => 'Configuration',
                'icon'  => 'fa-gear',
                'route' => 'seatnotifications.configuration',
            ],
        ],
    ],
];
