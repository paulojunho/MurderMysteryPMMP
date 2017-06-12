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

class MurderListener implements Listener {
	private $plugin;
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}
	public function onDamage(EntityDamageEvent $ev) {
		if($ev instanceof EntityDamageByEntityEvent) {
			/** @var Player $player */
			if(($player = $ev->getEntity()) instanceof Player and $this->plugin->inSession($player) != false) {
				$player->kill(); //kill the player in one hit if they are in the session and attacked by the murderer
			}
		}
	}
	public function onDeath(PlayerDeathEvent $ev) {
		if(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->getRole($ev->getPlayer()) == "detective") {
			$ev->setDrops([Item::get(Item::BOW), Item::get(Item::ARROW)]); // only drop bow if in session and a detective
		}elseif(($session = $this->plugin->inSession($ev->getPlayer())) != false) {
			$ev->setDrops([]); // no drops if in session and not a detective
		}
	}
	public function onSign(SignChangeEvent $ev) {
	    if($ev->isCancelled()) return;
	    $sign = $ev->getBlock();
	    $player = $ev->getPlayer();
	    if(stripos($ev->getLine(0),"murder") != false) {
	        //
        }
    }
}