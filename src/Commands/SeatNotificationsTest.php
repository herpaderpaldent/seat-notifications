<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 18:26
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Commands;


use Herpaderpaldent\Seat\SeatNotifications\Observers\RefreshTokenObserver;
use Herpaderpaldent\Seat\SeatNotifications\RefreshTokenDeleted;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Seat\Eveapi\Models\RefreshToken;

class SeatNotificationsTest extends Command
{

    protected $signature = 'seat-notifications:test';

    protected $description = 'This command adds and removes roles to all users depending on their SeAT-Group Association';

    public function __construct()
    {

        parent::__construct();
    }

    public function handle(RefreshTokenObserver $action)
    {


        $this->info('Test');
        $action->test();

        /*app('discord')->channel->createMessage([
            'channel.id' => 441330906356121622,
            'content' => 'Test my newly awesome bot'
        ]);*/

        //$this->notify(new RefreshTokenDeleted());
        //Notification::send(new RefreshTokenObserver() ,new RefreshTokenDeleted());

        //$when = now()->addMinutes(10);
        //$this->notify((new RefreshTokenDeleted())->delay($when)); //TODO: Check if that is working, according to docs it should
    }

}