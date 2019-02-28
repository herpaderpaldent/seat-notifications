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

namespace Herpaderpaldent\Seat\SeatNotifications\Channels\Slack;

use Exception;
use Herpaderpaldent\Seat\SeatNotifications\Jobs\SendSlackNotification;
use Illuminate\Notifications\Notification;

class SlackChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \JoliCode\Slack\Api\Client
     */
    protected $client;

    /**
     * Create a new Slack channel instance.
     */
    public function __construct()
    {

        $this->client = app('slack');
    }

    /**
     * @param                                        $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \Exception
     */
    public function send($notifiable, Notification $notification)
    {

        if (! $channel = $notifiable->driver_id) {
            throw new Exception('Channel could not be found.');
        }

        $message = $notification->toSlack($notifiable);

        $payload = $this->buildJsonPayload($message);

        $parameters = array_filter([
            'channel'     => $channel,
            'text'        => $payload['json']['text'],
            'attachments' => json_encode($payload['json']['attachments']),
        ]);

        $job = new SendSlackNotification($parameters);

        dispatch($job)->onQueue('high');
    }

    /**
     * Build up a JSON payload for the Slack webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage $message
     *
     * @return array
     */
    protected function buildJsonPayload(SlackMessage $message)
    {

        $optionalFields = array_filter([
            'channel'      => data_get($message, 'channel'),
            'icon_emoji'   => data_get($message, 'icon'),
            'icon_url'     => data_get($message, 'image'),
            'link_names'   => data_get($message, 'linkNames'),
            'unfurl_links' => data_get($message, 'unfurlLinks'),
            'unfurl_media' => data_get($message, 'unfurlMedia'),
            'username'     => data_get($message, 'username'),
        ]);

        return array_merge([
            'json' => array_merge([
                'text'        => $message->content,
                'attachments' => $this->attachments($message),
            ], $optionalFields),
        ], $message->http);
    }

    /**
     * Format the message's attachments.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage $message
     *
     * @return array
     */
    protected function attachments(SlackMessage $message)
    {

        return collect($message->attachments)->map(function ($attachment) use ($message) {

            return array_filter([
                'author_icon' => $attachment->authorIcon,
                'author_link' => $attachment->authorLink,
                'author_name' => $attachment->authorName,
                'color'       => $attachment->color ?: $message->color(),
                'fallback'    => $attachment->fallback,
                'fields'      => $this->fields($attachment),
                'footer'      => $attachment->footer,
                'footer_icon' => $attachment->footerIcon,
                'image_url'   => $attachment->imageUrl,
                'mrkdwn_in'   => $attachment->markdown,
                'text'        => $attachment->content,
                'thumb_url'   => $attachment->thumbUrl,
                'title'       => $attachment->title,
                'title_link'  => $attachment->url,
                'ts'          => $attachment->timestamp,
            ]);
        })->all();
    }

    /**
     * Format the attachment's fields.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackAttachment $attachment
     *
     * @return array
     */
    protected function fields(SlackAttachment $attachment)
    {

        return collect($attachment->fields)->map(function ($value, $key) {

            if ($value instanceof SlackAttachmentField) { //SlackAttachmentField
                return $value->toArray();
            }

            return ['title' => $key, 'value' => $value, 'short' => true];
        })->values()->all();
    }
}
