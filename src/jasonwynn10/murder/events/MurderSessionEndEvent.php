<?php
namespace jasonwynn10\murder\events;

use jasonwynn10\murder\objects\MurderSession;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class MurderSessionEndEvent extends MurderSessionEvent {
	/** @var string $winner */
	protected $winner;

	public function __construct(Plugin $plugin, MurderSession $session, Player $winner) {
		parent::__construct($plugin, $session);
		$this->winner = $winner->getName();
	}

	public function getWinner() : string {
		return $this->winner;
	}

	public function setWinner(string $winner) : self {
		$this->winner = $winner;
		return $this;
	}
}