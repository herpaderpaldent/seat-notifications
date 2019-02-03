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

class SlackAttachmentField
{
    /**
     * The title field of the attachment field.
     *
     * @var string
     */
    protected $title;

    /**
     * The content of the attachment field.
     *
     * @var string
     */
    protected $content;

    /**
     * Whether the content is short.
     *
     * @var bool
     */
    protected $short = true;

    /**
     * Set the title of the field.
     *
     * @param  string $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the content of the field.
     *
     * @param  string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Indicates that the content should not be displayed side-by-side with other fields.
     *
     * @return $this
     */
    public function long()
    {
        $this->short = false;

        return $this;
    }

    /**
     * Get the array representation of the attachment field.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'value' => $this->content,
            'short' => $this->short,
        ];
    }
}
