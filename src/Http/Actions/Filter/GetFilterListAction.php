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

use Illuminate\Support\Collection;
use Seat\Services\Repositories\Character\Character;

class GetFilterListAction
{
    use Character;

    private $get_corporation_filter;

    private $get_character_filter;

    public function __construct(GetCorporationFilter $get_corporation_filter, GetCharacterFilter $get_character_filter)
    {
        $this->get_corporation_filter = $get_corporation_filter;
        $this->get_character_filter = $get_character_filter;
    }

    public function execute(array $data) :Collection
    {

        switch ($data['filter']) {
            case 'characters':
                return $this->get_character_filter->execute($data);
                break;
            case 'corporations':
                return $this->get_corporation_filter->execute($data);
                break;
            default:
                return collect();
        }
    }
}
