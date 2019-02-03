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

namespace Herpaderpaldent\Seat\SeatNotifications\Observers;

use Herpaderpaldent\Seat\SeatNotifications\Jobs\KillmaillDispatcher;
use Seat\Eveapi\Models\Killmails\KillmailDetail;

class KillmailDetailObserver
{
    /**
     * Listen to the CorproationKillmail created event.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $corporation_killmail
     *
     * @return void
     */
    public function created(KillmailDetail $killmail_detail)
    {

        logger()->debug('Observer is dispatching an Killmail Notification for killmail_id: ' . $killmail_detail->killmail_id);

        KillmaillDispatcher::dispatch($killmail_detail)
            ->delay(now()->addMinutes(1))
            ->onQueue('high');
    }

    public function test()
    {
        $killmail_detail = KillmailDetail::first();

        $job = new KillmaillDispatcher($killmail_detail);

        dispatch($job)->onQueue('high');
    }
}
