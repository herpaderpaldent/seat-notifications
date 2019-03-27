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

namespace Herpaderpaldent\Seat\SeatNotifications\Http\Actions\Filter;

use Herpaderpaldent\Seat\SeatNotifications\Http\Actions\GetPublicDriverId;
use Herpaderpaldent\Seat\SeatNotifications\Models\NotificationSubscription;
use Illuminate\Support\Collection;
use Seat\Services\Repositories\Character\Character;

class GetCharacterFilter
{
    use Character;

    private $get_public_driver_id;

    private $subscribed_character_ids;

    public function __construct(GetPublicDriverId $get_public_driver_id)
    {

        $this->get_public_driver_id = $get_public_driver_id;
        $this->subscribed_character_ids = collect();
    }

    public function execute(array $data): Collection
    {

        NotificationSubscription::select('affiliations')
            ->wherehas('recipient', function ($query) use ($data) {

                $query->where('driver_id', $this->get_public_driver_id->execute($data));
            })
            ->where('notification', $data['notification'])
            ->get()
            ->each(function ($subscription) {

                $affiliations = $subscription->affiliations();

                if (array_key_exists('characters', $affiliations))
                    collect($affiliations->characters)->each(function ($id) {

                        $this->subscribed_character_ids->push($id);
                    });
            });

        return $this->getAllCharactersWithAffiliations(false)
            ->select('character_id', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($character) {

                return [
                    'id'         => $character->character_id,
                    'name'       => $character->name,
                    'subscribed' => in_array($character->character_id, $this->subscribed_character_ids->toArray()),
                ];
            });

    }
}
