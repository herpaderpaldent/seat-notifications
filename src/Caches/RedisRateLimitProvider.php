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

namespace Herpaderpaldent\Seat\SeatNotifications\Caches;

use Illuminate\Support\Facades\Redis;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RestCord\RateLimit\Provider\AbstractRateLimitProvider;

class RedisRateLimitProvider extends AbstractRateLimitProvider
{
    /**
     * @param RequestInterface $request
     * @return string
     */
    public function getKey(RequestInterface $request)
    {
        $prefix = 'seat-discord-notification';
        $path = $request->getUri()->getPath();

        return $prefix . ':' . strtolower($request->getMethod()) . strtr($path, ['/' => '.']);
    }

    /**
     * Returns when the last request was made.
     *
     * @param RequestInterface $request
     *
     * @return float|null When the last request was made.
     */
    public function getLastRequestTime(RequestInterface $request)
    {
        $key = $this->getKey($request);

        return Redis::hexists($key, 'last_request') ? floatval(Redis::hget($key, 'last_request')) : 0;
    }

    /**
     * Used to set the current time as the last request time to be queried when
     * the next request is attempted.
     *
     * @param RequestInterface $request
     */
    public function setLastRequestTime(RequestInterface $request)
    {
        $key = $this->getKey($request);

        Redis::hset($key, 'last_request', $this->getRequestTime($request));
    }

    /**
     * Returns the minimum amount of time that is required to have passed since
     * the last request was made. This value is used to determine if the current
     * request should be delayed, based on when the last request was made.
     *
     * Returns the allowed  between the last request and the next, which
     * is used to determine if a request should be delayed and by how much.
     *
     * @param RequestInterface $request The pending request.
     *
     * @return float The minimum amount of time that is required to have passed
     *               since the last request was made (in microseconds).
     */
    public function getRequestAllowance(RequestInterface $request)
    {
        $key = $this->getKey($request);

        return Redis::hexists($key, 'reset') ? (floatval(Redis::hget($key, 'reset')) - time()) * 1000000 : 0;
    }

    /**
     * Used to set the minimum amount of time that is required to pass between
     * this request and the next (in microseconds).
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response The resolved response.
     */
    public function setRequestAllowance(RequestInterface $request, ResponseInterface $response)
    {
        $key = $this->getKey($request);

        $remaining = $response->getHeaderLine('X-RateLimit-Remaining');
        $reset = $response->getHeaderLine('X-RateLimit-Reset');

        if (empty($remaining) || empty($response) || (int) $remaining > 0) {
            return;
        }

        Redis::hset($key, 'reset', $reset);
    }
}
