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

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Actions;

class BuildAffiliationJSONAction
{
    public function execute(array $data)
    {

        if (array_key_exists('characters_filter', $data) || array_key_exists('corporations_filter', $data)) {

            // retrieve filters and merge them together
            $characters_filter = null;
            $corporations_filter = null;

            if (array_key_exists('characters_filter', $data))
                $characters_filter = $data['characters_filter'] ?: [0];

            if (array_key_exists('corporations_filter', $data))
                $corporations_filter = $data['corporations_filter'] ?: [0];

            // If private subscription has character filter add affiliation from session
            if (request()->session()->has('herpaderp.seatnotifications.subscribe.characters_filter'))
                $characters_filter = request()->session()->pull('herpaderp.seatnotifications.subscribe.characters_filter');

            // If private subscription has corporation filter add affiliation from session
            if (request()->session()->has('herpaderp.seatnotifications.subscribe.corporations_filter'))
                $corporations_filter = request()->session()->pull('herpaderp.seatnotifications.subscribe.corporations_filter');

            $affiliations = json_encode(array_filter([
                'characters'   => $characters_filter,
                'corporations' => $corporations_filter,
            ]));

            return $affiliations;

        }

        return null;
    }
}
