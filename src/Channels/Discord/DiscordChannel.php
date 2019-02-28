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

namespace Herpaderpaldent\Seat\SeatNotifications\Channels\Discord;

use Herpaderpaldent\Seat\SeatNotifications\Exceptions\InvalidMessage;
use Herpaderpaldent\Seat\SeatNotifications\Jobs\SendDiscordNotification;
use Illuminate\Notifications\Notification;

class DiscordChannel
{
    /**
     * @var \RestCord\DiscordClient
     */
    protected $discord;

    public function send($notifiable, Notification $notification)
    {

        if (! $channel = $notifiable->driver_id) {
            return;
        }

        $message = $notification->toDiscord($notifiable);

        $payload = $this->buildJSONPayload($message);

        $parameters = [
            'channel.id' => (int) $channel,
            'content'    => $payload['content'],
            'embed'      => $payload['embeds'][0],
        ];

        $job = new SendDiscordNotification((int) $channel, $parameters);

        dispatch($job)->onQueue('high');
    }

    /**
     * Build up a payload for the Discord Webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage $message
     *
     * @return array
     */
    protected function buildPayload(DiscordMessage $message)
    {

        if ($this->checkMessageEmpty($message)) {
            throw InvalidMessage::cannotSendAnEmptyMessage();
        }
        if (! is_null($message->file)) {
            return $this->buildMultipartPayload($message);
        }

        return $this->buildJSONPayload($message);
    }

    /**
     * Checks if the given Message is valid.
     *
     * @param
     *
     * @return bool
     */
    protected function checkMessageEmpty($message)
    {

        if (is_null($message->content) && is_null($message->file) && is_null($message->embeds)) {
            return true;
        }

        return false;
    }

    /**
     * Build up a JSON payload for the Discord Webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage $message
     *
     * @return array
     */
    protected function buildJSONPayload(DiscordMessage $message)
    {

        $optionalFields = array_filter([
            'username'   => data_get($message, 'username'),
            'avatar_url' => data_get($message, 'avatar_url'),
            'tts'        => data_get($message, 'tts'),
            'timestamp'  => data_get($message, 'timestamp'),
        ]);

        return array_merge([
            'content' => $message->content,
            'embeds'  => $this->embeds($message),
        ], $optionalFields);
    }

    /**
     * Build up a Multipart payload for the Discord Webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage $message
     *
     * @return array
     */
    protected function buildMultipartPayload(DiscordMessage $message)
    {

        if (! is_null($message->embeds)) {
            throw InvalidMessage::embedsNotSupportedWithFileUploads();
        }
        $this->type = 'multipart';

        return collect($message)->forget('file')->reject(function ($value) {

            return is_null($value);
        })->map(function ($value, $key) {

            return ['name' => $key, 'contents' => $value];
        })->push($message->file)->values()->all();
    }

    /**
     * Format the message's embedded content.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage $message
     *
     * @return array
     */
    protected function embeds(DiscordMessage $message)
    {

        return collect($message->embeds)->map(function (DiscordEmbed $embed) {

            return array_filter([
                'color'       => $embed->color,
                'title'       => $embed->title,
                'description' => $embed->description,
                'link'        => $embed->url,
                'thumbnail'   => $embed->thumbnail,
                'image'       => $embed->image,
                'footer'      => $embed->footer,
                'author'      => $embed->author,
                'fields'      => $embed->fields,
            ]);
        })->all();
    }
}
