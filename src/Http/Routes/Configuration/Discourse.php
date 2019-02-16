<?php

Route::post('/', [
    'as' => 'herpaderp.seatnotifications.discourse.post.configuration',
    'uses' => 'DiscourseNotificationChannelController@postConfiguration',
]);
