<?php
namespace jasonwynn10\murder\events;

use pocketmine\entity\Human;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Sign;

use jasonwynn10\murder\Main;

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
	 *
	 *
	 * @param PlayerDeathEvent $ev
	 */
	public function onDeath(PlayerDeathEvent $ev) {
		if(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive() and $session->getRole($ev->getPlayer()) == "detective") {
			$ev->setDrops([Item::get(Item::BOW), Item::get(Item::ARROW)]); // only drop bow if in session and a detective
		}elseif(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive()) {
			$ev->setDrops([]); // no drops if in session and not a detective
		}
		if(($session = $this->plugin->inSession($ev->getPlayer())) != false and $session->isActive() and $this->plugin->getConfig()->get("corpse",false) == true) { //corpse after death?
			$nbt = new CompoundTag;
			$nbt->Pos = new ListTag("Pos", [
				new DoubleTag(0, $ev->getPlayer()->getX()),
				new DoubleTag(1, $ev->getPlayer()->getY()),
				new DoubleTag(2, $ev->getPlayer()->getZ())
			]);
			$nbt->Motion = new ListTag("Motion", [
				new DoubleTag(0, 0),
				new DoubleTag(1, 0),
				new DoubleTag(2, 0)
			]);
			$nbt->Rotation = new ListTag("Rotation", [
				new FloatTag(0, $ev->getPlayer()->getYaw()),
				new FloatTag(1, $ev->getPlayer()->getPitch())
			]);
			$nbt->Health = new ShortTag("Health", 1);
			$ev->getPlayer()->saveNBT();
			$nbt->Inventory = clone $ev->getPlayer()->namedtag->Inventory;
			$nbt->Skin = new CompoundTag("Skin", ["Data" => new StringTag("Data", $ev->getPlayer()->getSkinData()), "Name" => new StringTag("Name", $ev->getPlayer()->getSkinId())]);
			$entity = Human::createEntity("Human",$ev->getPlayer()->getLevel(),$nbt);
			$entity->setDataFlag(Human::DATA_PLAYER_FLAGS, Human::DATA_PLAYER_FLAG_SLEEP, true, Human::DATA_TYPE_BYTE);
		}
	}

	/**
	 * @param SignChangeEvent $ev
	 */
	public function onSign(SignChangeEvent $ev) {
		if($ev->isCancelled()) return;
		$sign = $ev->getBlock();
		$player = $ev->getPlayer();
		if(stripos($ev->getLine(0),"murder") != false and ($player->hasPermission("murder.mystery.sign.make") or $player->hasPermission("murder.mystery.sign"))) {
			$signTile = $sign->getLevel()->getTile($sign);
			if($signTile instanceof Sign) {
				$text = $signTile->getText();
				if(in_array($text[1],$this->plugin->getMaps())) {
					$signTile->setText("","","",""); // TODO set map names and player counts
					$this->plugin->addSign($signTile);
				}else{
					$player->sendMessage($this->plugin->getLanguage()->translateString("sign.invalidMap",[$text[1]]));
				}
			}
		}
	}
}