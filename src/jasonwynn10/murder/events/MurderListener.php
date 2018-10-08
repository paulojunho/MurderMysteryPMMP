<?php
namespace jasonwynn10\murder\events;

use jasonwynn10\murder\Main;
use pocketmine\entity\Human;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Sign;

class MurderListener implements Listener {
	private $plugin;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @param EntityDamageEvent $ev
	 */
	public function onDamage(EntityDamageEvent $ev) {
		if($ev instanceof EntityDamageByEntityEvent) {
			/** @var Player $player */
			if(($player = $ev->getEntity()) instanceof Player and ($session = $this->plugin->inSession($player)) != false and $session->isActive()) {
				$player->kill(); //kill the player in one hit if they are in the session and attacked by the murderer
			}
		}
	}

	/**
	 * @param PlayerDeathEvent $ev
	 */
	public function onDeath(PlayerDeathEvent $ev) {
		if(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive() and $session->getRole($ev->getPlayer()) == "detective") {
			$ev->setDrops([Item::get(Item::BOW), Item::get(Item::ARROW)]); // only drop bow if in session and a detective
		}elseif(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive()) {
			$ev->setDrops([]); // no drops if in session and not a detective
		}
		if(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive() and $this->plugin->getConfig()->get("Corpse", false) == true) { //corpse after death?
			$nbt = Human::createBaseNBT($ev->getPlayer(),new Vector3(),$ev->getPlayer()->getYaw(), $ev->getPlayer()->getPitch());
			$nbt->setShort("Health", 1);
			$ev->getPlayer()->saveNBT();
			if($ev->getPlayer()->namedtag->hasTag("Inventory"))
				$nbt->setTag(clone $ev->getPlayer()->namedtag->getListTag("Inventory"));
			if($ev->getPlayer()->namedtag->hasTag("Skin"))
				$nbt->setTag(clone $ev->getPlayer()->namedtag->getCompoundTag("Skin"));
			/** @var Human $entity */
			$entity = Human::createEntity("Human", $ev->getPlayer()->getLevel(), $nbt);
			$entity->setDataFlag(Human::DATA_PLAYER_FLAGS, Human::DATA_PLAYER_FLAG_SLEEP, true, Human::DATA_TYPE_BYTE);
		}
	}

	/**
	 * @param SignChangeEvent $ev
	 */
	public function onSign(SignChangeEvent $ev) {
		if($ev->isCancelled()) {
			return;
		}
		$sign = $ev->getBlock();
		$player = $ev->getPlayer();
		if(stripos($ev->getLine(0), "murder") != false and ($player->hasPermission("murder.mystery.sign.make") or $player->hasPermission("murder.mystery.sign"))) {
			$signTile = $sign->getLevel()->getTile($sign);
			if($signTile instanceof Sign) {
				$text = $signTile->getText();
				if(in_array($text[1], $this->plugin->getMaps())) {
					$signTile->setText("", "", "", ""); // TODO set map names and player counts
					$this->plugin->addSign($signTile);
				}else {
					$player->sendMessage($this->plugin->getLanguage()->translateString("sign.invalidMap", [$text[1]]));
				}
			}
		}
	}
}