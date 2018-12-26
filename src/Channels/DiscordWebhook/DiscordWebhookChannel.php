<?php

/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 05.07.2018
 * Time: 16:02
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook;

use Herpaderpaldent\Seat\SeatNotifications\Exceptions\CouldNotSendNotification;
use Herpaderpaldent\Seat\SeatNotifications\Exceptions\InvalidMessage;
use Illuminate\Notifications\Notification;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;


class DiscordWebhookChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * The Content-Type for the HTTP Request.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new Discord Webhook channel instance.
     *
     * @param \GuzzleHttp\Client $http
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
        $this->type = 'json';
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $url = $notification->toDiscordWebhook($notifiable)->url) {
            return;
        }
        $message = $notification->toDiscordWebhook($notifiable);

        $payload = $this->buildPayload($message);

        try {
            $response = $this->http->post($url, [$this->type => $payload]);
            return $this->getResponse($response);
        } catch (ClientException $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e);
        } catch (\Exception $e) {
            throw CouldNotSendNotification::couldNotCommunicateWithDiscordWebhook($e->getMessage());
        }

        /*return $this->discord->send($url, [
            //'content' => $message->body,
            'embeds' => json_encode($message->embed),
        ]);*/

        /*return $this->discord->send($url, [
            'embed' => [
                "title"
            ]
        ]);*/
    }
    /**
     * Get the response for the sent notification.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array
     */
    protected function getResponse(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        if ($code == 200) {
            return $this->getMessage($response->getBody()->getContents());
        }
        return [
            'StatusCode' => $code,
            'ReasonPhrase' => $response->getReasonPhrase(),
        ];
    }
    /**
     * Get the message that has been sent to Discord.
     *
     * @param string $content
     *
     * @return string|array
     */
    protected function getMessage($content)
    {
        $obj = json_decode($content);
        if ($obj) {
            $content = $obj;
        }
        return $content;
    }

    /**
     * Build up a payload for the Discord Webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordMessage $message
     *
     * @return array
     *
     * @throws \Herpaderpaldent\Seat\SeatNotifications\Exceptions\InvalidMessage
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
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordMessage $message
     *
     * @return array
     */
    protected function buildJSONPayload(DiscordMessage $message)
    {
        $optionalFields = array_filter([
            'username' => data_get($message, 'username'),
            'avatar_url' => data_get($message, 'avatar_url'),
            'tts' => data_get($message, 'tts'),
            'timestamp' => data_get($message, 'timestamp'),
        ]);
        return array_merge([
            'content' => $message->content,
            'embeds' => $this->embeds($message),
        ], $optionalFields);
    }

    /**
     * Build up a Multipart payload for the Discord Webhook.
     *
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordMessage $message
     *
     * @return array
     *
     * @throws \Herpaderpaldent\Seat\SeatNotifications\Exceptions\InvalidMessage
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
     * @param \Herpaderpaldent\Seat\SeatNotifications\Channels\DiscordWebhook\DiscordMessage $message
     *
     * @return array
     */
    protected function embeds(DiscordMessage $message)
    {
        return collect($message->embeds)->map(function (DiscordEmbed $embed) {
            return array_filter([
                'color' => $embed->color,
                'title' => $embed->title,
                'description' => $embed->description,
                'link' => $embed->url,
                'thumbnail' => $embed->thumbnail,
                'image' => $embed->image,
                'footer' => $embed->footer,
                'author' => $embed->author,
                'fields' => $embed->fields,
            ]);
        })->all();
    }
}