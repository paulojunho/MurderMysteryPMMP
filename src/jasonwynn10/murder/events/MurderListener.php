<?php
namespace jasonwynn10\murder\events;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\Player;

use jasonwynn10\murder\Main;
use pocketmine\tile\Sign;

class MurderListener implements Listener {
	private $plugin;
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}
	public function onDamage(EntityDamageEvent $ev) {
		if($ev instanceof EntityDamageByEntityEvent) {
			/** @var Player $player */
			if(($player = $ev->getEntity()) instanceof Player and ($session = $this->plugin->inSession($player)) != false and $session->isActive()) {
				$player->kill(); //kill the player in one hit if they are in the session and attacked by the murderer
			}
		}
	}
	public function onDeath(PlayerDeathEvent $ev) {
		if(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive() and $session->getRole($ev->getPlayer()) == "detective") {
			$ev->setDrops([Item::get(Item::BOW), Item::get(Item::ARROW)]); // only drop bow if in session and a detective
		}elseif(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive()) {
			$ev->setDrops([]); // no drops if in session and not a detective
		}
	}
	public function onSign(SignChangeEvent $ev) {
	    if($ev->isCancelled()) return;
	    $sign = $ev->getBlock();
	    $player = $ev->getPlayer();
	    if(stripos($ev->getLine(0),"murder") != false and ($player->hasPermission("murder.mystery.sign.make") or $player->hasPermission("murder.mystery.sign"))) {
	        $signTile = $sign->getLevel()->getTile($sign);
	        if($signTile instanceof Sign) {
	            $text = $signTile->getText();
	            if(in_array($text[1],$this->plugin->getMaps())) {
                    $signTile->setText();
                    $this->plugin->addSign($signTile);
                }else{
	                $player->sendMessage($this->plugin->getLanguage()->translateString("sign.invalidMap",[$text[1]]));
                }
            }
        }
    }
}