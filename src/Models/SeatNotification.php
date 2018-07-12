<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 10.07.2018
 * Time: 21:53
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Models;


use Illuminate\Database\Eloquent\Model;

class Seatnotification extends Model
{
    protected $fillable = ['character_id','corporation_id','method','notification','webhook' ];

}