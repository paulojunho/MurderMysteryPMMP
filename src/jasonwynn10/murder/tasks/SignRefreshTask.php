<?php
declare(strict_types=1);
namespace jasonwynn10\murder\tasks;

use jasonwynn10\murder\Main;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class SignRefreshTask extends Task {
	/** @var Main $plugin */
	protected $plugin;

	public function __construct(Main $owner) {
		$this->plugin = $owner;
	}

	public function onRun($currentTick) {
		foreach($this->plugin->getSigns() as $sign) {
			$text = $sign->getLine(1);
			if(in_array($text, $this->plugin->getMaps())) {
				$session = $this->plugin->getMapSession($text);
				if($session !== null) {
					$SessionPlayers = $session->getPlayers();
				}
				$queuePlayers = $this->plugin->getMapQueue($text);
				$sign->setText(TextFormat::RESET . TextFormat::GREEN . "Murder Mystery", $text, count($SessionPlayers ?? []) > 0 ? "" : "In Queue:", count($SessionPlayers ?? []) > 0 ? "Game In Session" : count($queuePlayers)); // use text formatting as indicator for valid sign on sign loadings
				$this->plugin->addSign($sign);
			}
		}
	}
}