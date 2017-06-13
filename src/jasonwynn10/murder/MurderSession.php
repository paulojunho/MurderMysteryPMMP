<?php
namespace jasonwynn10\murder;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class MurderSession {
	/** string $sessionName */
	private $sessionName;
	/** string  */
	private $killer, $detective;
	/** string[] $innocent */
	private $innocent = [];
	/** @var Position[] $spawnArea */
	protected $spawnArea = [];
	private $active = false;
	public function __construct(string $sessionName, string $levelName, Position $playerSpawnA, Position $playerSpawnB, Player $killer = null, Player $detective = null, Player ...$innocent) {
		$this->sessionName = $sessionName;
		$this->levelName = $levelName;
		$this->spawnArea[0] = $playerSpawnA;
		$this->spawnArea[1] = $playerSpawnB;
		$this->killer = $killer->getName();
		$this->detective = $detective->getName();
		if($innocent = null) {
		    $innocent = [];
        }
		foreach($innocent as $pl) {
			$this->innocent[] = $pl->getName();
		}
	}
	public function getName() : string{
		return $this->sessionName;
	}
	public function getLevel() : string{
		return $this->levelName;
	}
	public function getPlayers() : array{
		$arr = $this->innocent;
		$arr["killer"] = $this->killer;
		$arr["detective"] = $this->detective;
		return $arr;
	}
	public function getRandomSpawnCoords() : Position{
		$x = mt_rand($this->spawnArea[0]->x, $this->spawnArea[1]->x);
		$y = mt_rand($this->spawnArea[0]->y, $this->spawnArea[1]->y);
		$z = mt_rand($this->spawnArea[0]->z, $this->spawnArea[1]->z);
		$level = Server::getInstance()->getLevelByName($this->levelName);
		return new Position($x, $y, $z, $level);
	}
	/**
	 * @var string|Player $player
	 * @return string
	 */
	public function getRole($player) : string{
		$player = $player instanceof Player ? $player->getName() : $player;
		$key = array_search($player, $this->getPlayers());
		if($key != "killer" and $key != "detective") {
			return "innocent";
		}
		return $key;
	}
	public function isActive() : bool{
		return $this->active;
	}
	public function setActive(bool $active = true) {
		$this->active = $active;
	}
}
