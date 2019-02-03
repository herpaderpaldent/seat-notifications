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

class DiscordEmbedField
{
    /**
     * The name of the field.
     *
     * @var string
     */
    public $name;
    /**
     * The value of the field.
     *
     * @var string
     */
    public $value;
    /**
     * Whether or not this field should display inline.
     *
     * @var bool
     */
    public $inline;

    /**
     * Create a new Embed Field instance.
     *
     * @param string $name
     * @param string $value
     * @param bool|null $inline
     */
    public function __construct($name, $value, $inline = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->inline = $inline;
    }

    /**
     * Set the name of the field.
     *
     * @param string $name
     *
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the value of the field.
     *
     * @param string $value
     *
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the name of the field.
     *
     * @param bool|null $inline
     *
     * @return $this
     */
    public function inline($inline = true)
    {
        $this->inline = boolval($inline);

        return $this;
    }

    /**
     * Get an array representation of the embedded field.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'inline' => $this->inline,
        ];
    }
}
