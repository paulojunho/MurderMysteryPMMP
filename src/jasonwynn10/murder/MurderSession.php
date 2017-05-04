<?php
namespace jasonwynn10\murder;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\math\Vector3;

class MurderSession {
	/** string $sessionName */
	private $sessionName;
	/** Player  */
	private $killer, $detective;
	/** Player[] $innocent */
	private $innocent = [];
	/** @var Position[] $spawnArea */
	protected $spawnArea = [];
	public function __construct(string $sessionName, string $levelName, Position $playerSpawnA, Position $playerSpawnB, Player $killer, Player $detective, Player ...$innocent) {
		$this->sessionName = $sessionName;
		$this->levelName = $levelName;
		$this->spawnArea[0] = $playerSpawnA;
		$this->spawnArea[1] = $playerSpawnB;
		$this->killer = $killer;
		$this->detective = $detective;
		$this->innocent = $innocent;
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
	public function getRandomSpawnCoords() : Vector3{
		$x = mt_rand($this->spawnArea[0]->x, $this->spawnArea[1]->x);
		$y = mt_rand($this->spawnArea[0]->y, $this->spawnArea[1]->y);
		$z = mt_rand($this->spawnArea[0]->z, $this->spawnArea[1]->z);
		return new Vector3($x, $y, $z);
	}
}
