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

namespace Herpaderpaldent\Seat\SeatNotifications\Drivers;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

/**
 * Class DiscourseChannel.
 * @package Herpaderpaldent\Seat\SeatNotifications\Providers\Discourse
 */
class DiscourseNotificationDriver implements INotificationDriver
{
    /**
     * The view name which will be used to store the channel settings.
     *
     * @return string
     */
    public static function getSettingsView(): string
    {
        return 'seatnotifications::discourse.settings';
    }

    /**
     * @return string
     */
    public static function getRegistrationView(): string
    {
        return 'seatnotifications::discourse.registration';
    }

    /**
     * @return string
     */
    public static function getButtonLabel() : string
    {
        return 'Discourse';
    }

    /**
     * @return string
     */
    public static function getButtonIconClass() : string
    {
        return 'fa-comments';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getChannels(): array
    {
        return cache()->remember('herpaderp.seatnotifications.discourse.channels', 5, function () {
            return self::getCategories()->map(function ($category) {
                return [
                    'name'            => $category->name,
                    'id'              => $category->id,
                    'private_channel' => false,
                ];
            })->all();
        });
    }

    /**
     * Determine if a channel is supporting private notifications.
     *
     * @return bool
     */
    public static function allowPersonalNotifications(): bool
    {
        return false;
    }

    /**
     * Determine if a channel is supporting public notifications.
     *
     * @return bool
     */
    public static function allowPublicNotifications(): bool
    {
        return true;
    }

    /**
     * Determine if a channel has been properly setup.
     *
     * @return bool
     */
    public static function isSetup(): bool
    {
        return ! is_null(setting('herpaderp.seatnotifications.discourse.credentials.api_key', true));
    }

    /**
     * @param int|null $category_id
     * @return Collection
     */
    private static function getCategories(int $category_id = null): Collection
    {
        $params = [
            'api_key' => setting('herpaderp.seatnotifications.discourse.credentials.api_key', true),
        ];

        if (! is_null($category_id))
            $params['parent_category_id'] = $category_id;

        $client = new Client([
            'base_uri' => setting('herpaderp.seatnotifications.discourse.credentials.url', true),
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        // request categories list
        $response = $client->get('/categories', [
            'query' => $params,
        ]);

        // decode the response
        $json = json_decode($response->getBody());

        // ensure there is an attribute called category_list which is containing the list
        if (! property_exists($json, 'category_list'))
            return collect();

        // ensure there is an attribute called categories which is containing the list
        if (! property_exists($json->category_list, 'categories'))
            return collect();

        // convert the list into a collection for convenient use
        $categories = collect($json->category_list->categories);

        // iterate over each category and retrieve sub-categories
        $categories->each(function ($category) use ($categories) {
            if (property_exists($category, 'subcategory_ids')) {

                // small hack to merge the existing list with the new one since Laravel will erase item based on key :(
                self::getCategories($category->id)->each(function ($sub_category) use ($categories) {
                    $categories->push($sub_category);
                });
            }
        });

        return $categories;
    }

    /**
     * Return channel_id of user.
     *
     * @return string
     */
    public static function getPrivateChannel(): ?string
    {
        return null;
    }

    /**
     * Return driver_id of public subscription.
     *
     * @param string $notification
     *
     * @return string
     */
    public static function getPublicDriverId(string $notification) : ?string
    {
        return null;
    }

    /**
     * Return the route key which have to be used in a private notification registration flow.
     *
     * @return string
     */
    public static function getPrivateRegistrationRoute(): ?string
    {
        return null;
    }
}
