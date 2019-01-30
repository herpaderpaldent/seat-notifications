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
use Illuminate\Http\Request;
use Seat\Eveapi\Models\Killmails\CorporationKillmail;
use Seat\Eveapi\Models\Killmails\KillmailAttacker;
use Seat\Eveapi\Models\Killmails\KillmailDetail;
use Seat\Eveapi\Models\Killmails\KillmailVictim;
use Seat\Eveapi\Models\Killmails\KillmailVictimItem;
use Seat\Eveapi\Models\Market\Price;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Eveapi\Models\Universe\UniverseName;

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

        $this->is_loss = $notifiable
            ->notifications
            ->firstwhere('name','kill_mail')
            ->hasAffiliation('corp', $this->killmail_detail->victims->corporation_id);

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

        // if it's a kill
        $title = sprintf('%s just killed %s',
            $this->getAttacker(),
            $this->getVictim($this->killmail_detail->killmail_id)
        );

        /*if($this->is_loss)
            $title = sprintf('')*/

        return (new DiscordMessage)
            ->embed(function ($embed) use ($title) {

                $embed->title($title)
                    ->thumbnail($this->image)
                    ->color($this->is_loss ? '14502713' : '42586')
                    ->field('Value', $this->getValue($this->killmail_detail->killmail_id))
                    ->field('System', $this->getSystem(), true)
                    ->field('Link', $this->zKillBoardToLink('kill', $this->killmail_detail->killmail_id), true)
                    ->footer('zKillboard ' . $this->killmail_detail->killmail_time, 'https://zkillboard.com/img/wreck.png');
                    //->field('Corporation', $this->corporation, true)
                    //->field('Main Character', $this->main_character, false);
            });
    }

    /**
     * @param $notifiable
     *
     * @return \Herpaderpaldent\Seat\SeatNotifications\Channels\Slack\SlackMessage
     */
    /*public function toSlack($notifiable)
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
    }*/

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

    private function getAttacker() :string
    {
        $killmail_attacker = $this->killmail_detail
            ->attackers
            ->where('final_blow', 1)
            ->first();

        return sprintf('**%s** (%s) flying a **%s**',
            //optional(UniverseName::find($killmail_attacker->character_id))->name,
            $this->resolveID($killmail_attacker->character_id),
            $this->resolveID($killmail_attacker->corporation_id),
            optional(InvType::find($killmail_attacker->ship_type_id))->typeName);
    }

    private function getVictim(int $killmail_id) :string
    {
        $killmail_victim = KillmailVictim::find($killmail_id);

        return sprintf('**%s** (%s) flying a **%s**',
            $this->resolveID($killmail_victim->character_id),
            $this->resolveID($killmail_victim->corporation_id),
            optional(InvType::find($killmail_victim->ship_type_id))->typeName);
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

    private function getSystem() : string
    {
        $solar_system = $this->killmail_detail->solar_system;

        return sprintf('[%s (%s)](%s)',
            $solar_system->itemName,
            number($solar_system->security, 2),
            $this->zKillBoardToLink('system', $solar_system->itemID)
        );
    }

    private function resolveID($id)
    {
        $cached_entry = cache('name_id:' . $id);

        if(! is_null($cached_entry))
            return $cached_entry;

        // Resolve the Esi client library from the IoC
        $eseye = app('esi-client')->get();
        $eseye->setBody([$id]);
        $names = $eseye->invoke('post', '/universe/names/');

        $name = collect($names)->first()->name;

        return $name;
    }





}