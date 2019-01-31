<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 23.01.2019
 * Time: 19:22
 */

namespace Herpaderpaldent\Seat\SeatNotifications\Notifications;


use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Discord\DiscordMessage;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackChannel;
use Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage;
use Seat\Eveapi\Models\Killmails\KillmailDetail;
use Seat\Eveapi\Models\Killmails\KillmailVictimItem;
use Seat\Eveapi\Models\Market\Price;
use Seat\Eveapi\Models\Sde\InvType;

class KillMailNotification extends BaseNotification
{
    /**
     * @var array
     */
    protected $tags = ['kill_mail'];

    /**
     * @var
     */
    private $killmail_detail;

    /**
     * @var bool
     */
    private $is_loss;

    private $zkillmail_link;

    private $image;

    /**
     * KillMailNotification constructor.
     *
     * @param \Seat\Eveapi\Models\Killmails\CorporationKillmail $corporation_killmail
     */
    public function __construct(int $killmail_id)
    {

        parent::__construct();

        $this->killmail_detail = KillmailDetail::find($killmail_id);
        $this->image = sprintf('https://imageserver.eveonline.com/Type/%d_64.png',
            $this->killmail_detail->victims->ship_type_id);



    }

    public function via($notifiable)
    {

        switch ($notifiable->notification_channel) {
            case 'discord':
                $this->tags = [
                    'kill_mail',
                    'discord',
                    $notifiable->type === 'private' ? $notifiable->recipient() : 'channel',
                ];
                return [DiscordChannel::class];
                break;
            case 'slack':
                $this->tags = [
                    'kill_mail',
                    'slack',
                    $notifiable->type === 'private' ? $notifiable->recipient() : 'channel',
                ];
                return [SlackChannel::class];
                break;
            default:
                return [''];

        }
    }

    public function toDiscord($notifiable)
    {

        return (new DiscordMessage)
            ->embed(function ($embed) use ($notifiable){

                $embed->title($this->getNotificationString('discord'))
                    ->thumbnail($this->image)
                    ->color($this->is_loss($notifiable) ? '14502713' : '42586')
                    ->field('Value', $this->getValue($this->killmail_detail->killmail_id))
                    ->field('Involved Pilots', $this->getNumberOfAttackers(), true)
                    ->field('System', $this->getSystem(), true)
                    ->field('Link', $this->zKillBoardToLink('kill', $this->killmail_detail->killmail_id), true)
                    ->footer('zKillboard ' . $this->killmail_detail->killmail_time, 'https://zkillboard.com/img/wreck.png');
            });
    }

    /**
     * @param $notifiable
     *
     * @return \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->warning()
            ->attachment(function ($attachment) {
                $attachment->title('RefreshTokenDeleted')
                    ->fields([
                        'Character' => $this->user_name,
                        'Corporation' => $this->corporation,
                        'Main Character' => $this->main_character,
                    ])
                    ->thumb($this->image);
            });
    }

    /**
     * Build a link to zKillboard using Slack message formatting.
     *
     * @param string $type (must be ship, character, corporation or alliance)
     * @param int    $id   the type entity ID
     * @param string $name the type name
     *
     * @return string
     */
    private function zKillBoardToLink(string $type, int $id)
    {

        if (! in_array($type, ['ship', 'character', 'corporation', 'alliance', 'kill', 'system']))
            return '';

        return sprintf('https://zkillboard.com/%s/%d/', $type, $id);
    }

    private function getNotificationString(string $channel) : string
    {
        return sprintf('%s just killed %s %s',
            $this->getAttacker($channel),
            $this->getVictim($channel),
            $this->getNumberOfAttackers($channel) === 1 ? 'solo.' : ''
        );
    }

    private function getAttacker($channel) :string
    {
        $killmail_attacker = $this->killmail_detail
            ->attackers
            ->where('final_blow', 1)
            ->first();

        if($channel === 'discord')
            return $this->getDiscordKMStringPartial(
                $killmail_attacker->character_id,
                $killmail_attacker->corporation_id,
                $killmail_attacker->ship_type_id,
                $killmail_attacker->alliance_id
            );

        return '';
    }

    /**
     * @param int $killmail_id
     *
     * @return string
     */
    private function getVictim($channel) :string
    {
        $killmail_victim = $this->killmail_detail->victims;

        if($channel === 'discord')
            return $this->getDiscordKMStringPartial(
                $killmail_victim->character_id,
                $killmail_victim->corporation_id,
                $killmail_victim->ship_type_id,
                $killmail_victim->alliance_id
            );

        return '';
    }

    private function getNumberOfAttackers() : int
    {

        return $this->killmail_detail->attackers->count();
    }

    private function getDiscordKMStringPartial($character_id, $corporation_id, $ship_type_id, $alliance_id) : string
    {
        $character = is_null($character_id) ? null : $this->resolveID($character_id);
        $corporation = $this->resolveID($corporation_id);
        $alliance = is_null($alliance_id) ? null :  strtoupper(' ' . $this->resolveID($alliance_id, true));
        $ship_type = optional(InvType::find($ship_type_id))->typeName;

        if(is_null($character_id))
            return sprintf('**%s** (%s%s)',
                $ship_type,
                $corporation,
                $alliance
            );

        if (!is_null($character_id))
            return sprintf('**%s** (%s%s) flying a **%s**',
                $character,
                $corporation,
                $alliance,
                $ship_type
            );

        return '';
    }

    private function getValue(int $killmail_id) :string
    {
        $value = KillmailVictimItem::where('killmail_id', $killmail_id)
            ->get()
            ->map(function ($item) {
                return Price::find($item->item_type_id);
            })
            ->push(Price::find($this->killmail_detail->victims->ship_type_id))
            ->sum('average_price');

        return number($value) . ' ISK';
    }

    private function is_loss($notifiable) : bool
    {
        return $notifiable
            ->notifications
            ->firstwhere('name','kill_mail')
            ->hasAffiliation('corp', $this->killmail_detail->victims->corporation_id);
    }

    private function getSystem() : string
    {
        $solar_system = $this->killmail_detail->solar_system;

        return sprintf('[%s (%s)](%s)',
            $solar_system->itemName,
            number($solar_system->security, 2),
            $this->zKillBoardToLink('system', $solar_system->itemID)
        );
    }

    private function resolveID($id, $is_alliance = false)
    {
        $cached_entry = cache('name_id:' . $id);

        if(! is_null($cached_entry))
            return $cached_entry;

        if($is_alliance)
            return $this->getAllianceTicker($id);

        // Resolve the Esi client library from the IoC
        $eseye = app('esi-client')->get();
        $eseye->setBody([$id]);
        $names = $eseye->invoke('post', '/universe/names/');

        var_dump($names);

        $name = collect($names)->first()->name;

        return $name;
    }

    private function getAllianceTicker($id)
    {
        $cached_entry = cache('alliance_ticker:' . $id);

        if(! is_null($cached_entry))
            return $cached_entry;

        // Resolve the Esi client library from the IoC
        $eseye = app('esi-client')->get();
        $ticker = $eseye->invoke('get', '/alliances/' . $id)->ticker;

        cache(['alliance_ticker:' . $id => $ticker], carbon()->addCentury());

        return $ticker;
    }





}