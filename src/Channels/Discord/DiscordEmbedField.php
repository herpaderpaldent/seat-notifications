<?php
/**
 * Created by PhpStorm.
 *  * User: Herpaderp Aldent
 * Date: 07.07.2018
 * Time: 22:54.
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
