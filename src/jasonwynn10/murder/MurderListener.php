<?php
namespace jasonwynn10\murder;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;

class MurderListener implements Listener {
	private $plugin;
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}
	public function onDamage(EntityDamageEvent $ev) {
		if($ev instanceof EntityDamageByEntityEvent) {
			//
		}
		/** @var Player $player */
		if(($player = $ev->getEntity()) instanceof Player and $this->plugin->inSession($player)) {
			$player->kill(); //kill the player in one hit if they are in the session and attacked by the murderer
		}
	}
	//TODO cancel item drops
}
