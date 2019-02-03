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

use Closure;

class DiscordMessage
{

    /**
     * The message contents (up to 2000 characters).
     *
     * @var string
     */
    public $content;

    /**
     * true if this is a TTS message.
     *
     * @var string|null
     */
    public $tts = 'false';

    /**
     * The contents of the file being sent.
     *
     * @var array
     */
    public $file;

    /**
     * Embedded rich content.
     *
     * @var array
     */
    public $embeds;

    /**
     * Allows to set the content by creation.
     *
     * @param string $content
     */
    public function __construct($content = null)
    {
        if (! is_null($content)) {
            $this->content($content);
        }
    }

    /**
     * Set the content of the message.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Send as a TTS message.
     *
     * @param bool|null $enabled
     *
     * @return $this
     */
    public function tts($enabled = true)
    {
        $this->tts = $enabled ? 'true' : 'false';

        return $this;
    }

    /**
     * Set the contents and filename of the file being sent.
     *
     * @param string $contents
     * @param string $filename
     *
     * @return $this
     */
    public function file($contents, $filename)
    {
        $this->file = [
            'name' => 'file',
            'contents' => $contents,
            'filename' => $filename,
        ];

        return $this;
    }

    /**
     * Define an embedded rich content for the message.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function embed(Closure $callback)
    {
        $this->embeds[] = $embed = new DiscordEmbed;
        $callback($embed);

        return $this;
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'content' => $this->content,
            'tts' => $this->tts,
            'file' => $this->file,
            'embeds' => $this->embeds,
        ];
    }
}
