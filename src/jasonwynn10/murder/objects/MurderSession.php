<?php
namespace jasonwynn10\murder\objects;

use pocketmine\Player;

class MurderSession {
	/** string $sessionName */
	protected $sessionName;
	/** string  */
	protected $killer, $detective;
	/** string[] $innocent */
	protected $innocent = [];
	/** @var bool $active */
	protected $active = false;
	/** @var GameMap $map */
	protected $map;

	public function __construct(string $sessionName, GameMap $map, string $killer, string $detective, string ...$innocent) {
		$this->sessionName = $sessionName;
		$this->map = $map;
		$this->killer = $killer;
		$this->detective = $detective;
		foreach($innocent as $player) {
			$this->innocent[] = $player;
		}
	}

	public function getName() : string {
		return $this->sessionName;
	}

	public function getLevel() : string {
		return $this->map;
	}

	/**
	 * @var string|Player $player
	 * @return string
	 */
	public function getRole($player) : string {
		$player = $player instanceof Player ? $player->getName() : $player;
		$key = array_search($player, $this->getPlayers());
		if($key != "killer" and $key != "detective") {
			return "innocent";
		}
		return $key;
	}

	public function getPlayers() : array {
		$arr = $this->innocent;
		$arr["killer"] = $this->killer;
		$arr["detective"] = $this->detective;
		return $arr;
	}

	public function isActive() : bool {
		return $this->active;
	}

	public function setActive(bool $active = true) : self {
		$this->active = $active;
		return $this;
	}

	public function getMap() : GameMap {
		return $this->map;
	}
}