<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 10:03.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when a 4xx http error code is received.
     *
     * @param \GuzzleHttp\Exception\ClientException $exception
     *
     * @return static
     */
    public static function serviceRespondedWithAnError(ClientException $exception)
    {
        $response = $exception->getResponse();
        $code = $response->getStatusCode();
        $message = $response->getBody()->getContents();

        return new static("Discord Webhook responded with an error ({$code}): {$message}");
    }

    /**
     * Thrown when the api is not reachable.
     *
     * @param string $message
     *
     * @return static
     */
    public static function couldNotCommunicateWithDiscordWebhook($message)
    {
        return new static("The communication with Discord Webhook failed. Reason: {$message}");
    }
}
