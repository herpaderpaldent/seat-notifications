<?php

namespace Herpaderpaldent\Seat\SeatNotifications\Exceptions;

use Exception;
use Throwable;

/**
 * Class ImplementPrivateFlowException.
 * @package Herpaderpaldent\Seat\SeatNotifications\Exceptions
 */
class ImplementPrivateFlowException extends Exception
{
    /**
     * ImplementPrivateFlowException constructor.
     * @param string $provider
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $provider, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('The private flow has not been fully implemented by the driver %s. ' .
            'Please ensure getPrivateChannel() and getPrivateRegistrationRoute() are returning a valid value.', $provider);

        parent::__construct($message, $code, $previous);
    }
}
