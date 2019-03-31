<?php
/**
 * MIT License.
 *
 * Copyright (c) 2019. Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
