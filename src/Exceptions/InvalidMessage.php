<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 23:12
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Exceptions;


class InvalidMessage extends \Exception
{
    /**
     * Thrown when a file upload contents an aembedded content.
     * Because uploading files require a multipart/form-data request.
     *
     * @return static
     */
    public static function embedsNotSupportedWithFileUploads()
    {
        return new static('Embedded Content is not supported with File Uploads.');
    }
    /**
     * Thrown when the message does not contain a content, file or message.
     *
     * @return static
     */
    public static function cannotSendAnEmptyMessage()
    {
        return new static('Cannot send an empty message');
    }
}