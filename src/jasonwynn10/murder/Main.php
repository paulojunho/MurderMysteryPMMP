<?php
namespace jasonwynn10\murder;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;

class Main extends PluginBase {
	public function onLoad() {
		$this->getCommandMap()->register(MurderMystery::class, new MurderMystery($this));
		$this->getServer()->getPluginManager()->registerEvents(new MurderListener($this), $this);
	}
	public function onEnable() {
		$this->saveDefaultConfig();
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}
	
	// API
	
	public function addQueue(Player $player) {
		//TODO
	}
	public function removeQueue(Player $player) {
		//TODO
	}
}
