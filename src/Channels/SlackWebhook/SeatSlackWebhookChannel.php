<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 26.07.2018
 * Time: 12:07
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook;

use Illuminate\Notifications\Notification;
use GuzzleHttp\Client as HttpClient;


class SeatSlackWebhookChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create a new Slack channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function send($notifiable, Notification $notification)
    {
        if(empty(setting("slack_webhook", true))){
            return;
        }

        $url = setting("slack_webhook", true);
        $this->http->post($url, $this->buildJsonPayload(
            $notification->toSeatSlack($notifiable)
        ));
    }

    /**
     * Build up a JSON payload for the Slack webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook\SeatSlackMessage $message
     *
     * @return array
     */
    protected function buildJsonPayload(SeatSlackMessage $message)
    {
        $optionalFields = array_filter([
            'channel' => data_get($message, 'channel'),
            'icon_emoji' => data_get($message, 'icon'),
            'icon_url' => data_get($message, 'image'),
            'link_names' => data_get($message, 'linkNames'),
            'unfurl_links' => data_get($message, 'unfurlLinks'),
            'unfurl_media' => data_get($message, 'unfurlMedia'),
            'username' => data_get($message, 'username'),
        ]);

        return array_merge([
            'json' => array_merge([
                'text' => $message->content,
                'attachments' => $this->attachments($message),
            ], $optionalFields),
        ], $message->http);
    }

    /**
     * Format the message's attachments.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook\SeatSlackMessage $message
     *
     * @return array
     */
    protected function attachments(SeatSlackMessage $message)
    {
        return collect($message->attachments)->map(function ($attachment) use ($message) {
            return array_filter([
                'author_icon' => $attachment->authorIcon,
                'author_link' => $attachment->authorLink,
                'author_name' => $attachment->authorName,
                'color' => $attachment->color ?: $message->color(),
                'fallback' => $attachment->fallback,
                'fields' => $this->fields($attachment),
                'footer' => $attachment->footer,
                'footer_icon' => $attachment->footerIcon,
                'image_url' => $attachment->imageUrl,
                'mrkdwn_in' => $attachment->markdown,
                'text' => $attachment->content,
                'thumb_url' => $attachment->thumbUrl,
                'title' => $attachment->title,
                'title_link' => $attachment->url,
                'ts' => $attachment->timestamp,
            ]);
        })->all();
    }

    /**
     * Format the attachment's fields.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\SlackWebhook\SeatSlackAttachment $attachment
     *
     * @return array
     */
    protected function fields(SeatSlackAttachment $attachment)
    {
        return collect($attachment->fields)->map(function ($value, $key) {
            if ($value instanceof SeatSlackAttachmentField) { //SlackAttachmentField
                return $value->toArray();
            }

            return ['title' => $key, 'value' => $value, 'short' => true];
        })->values()->all();
    }

}