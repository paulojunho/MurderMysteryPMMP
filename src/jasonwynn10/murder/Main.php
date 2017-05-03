<?php
namespace jasonwynn10\murder;

use pocketmine\lang\BaseLang;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {
	/** @var BaseLang $baseLang */
	private $baseLang = null;
	public function onLoad() {
		$this->getCommandMap()->register(MurderMystery::class, new MurderMystery($this));
		$this->getServer()->getPluginManager()->registerEvents(new MurderListener($this), $this);
	}
	public function onEnable() {
		$this->saveDefaultConfig();
		$lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
        	$this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}
	
	// API
	
	/**
	 * @api
	 * @return BaseLang
	 */
	public function getLanguage() : BaseLang {
		return $this->baseLang;
	}
	
	public function addQueue(Player $player) {
		//TODO
	}
	public function removeQueue(Player $player) {
		//TODO
	}
}
