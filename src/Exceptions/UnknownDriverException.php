<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 02.03.2019
 * Time: 22:40.
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Exceptions;

use Exception;

class UnknownDriverException extends Exception
{
    public function __construct(string $provider, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('An unknown driver implemention of %s was requested ', $provider);

        parent::__construct($message, $code, $previous);
    }
}
