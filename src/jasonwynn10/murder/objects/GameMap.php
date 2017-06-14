<?php
namespace jasonwynn10\murder\objects;

use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class GameMap {
	/** @var string $name */
	private $name;
	/** @var string $levelName */
	private $levelName;
	/** @var Vector3[] $spawnArea */
	private $spawnArea = [];
	/** @var Vector3[] $goldArea */
	private $goldArea = [];
	public function __construct(string $name, Vector3 $playerSpawnA, Vector3 $playerSpawnB, Vector3 $goldSpawnA, Vector3 $goldSpawnB, string $levelName, int $minimum, int $maximum) {
		$this->spawnArea[0] = $playerSpawnA;
		$this->spawnArea[1] = $playerSpawnB;
		$this->goldArea[0] = $goldSpawnA;
		$this->goldArea[1] = $goldSpawnB;
		$this->levelName = $levelName;
		$this->name = $name;
	}
	public function getRandomPlayerSpawnCoords() : Position{
		$x = mt_rand($this->spawnArea[0]->x, $this->spawnArea[1]->x);
		$y = mt_rand($this->spawnArea[0]->y, $this->spawnArea[1]->y);
		$z = mt_rand($this->spawnArea[0]->z, $this->spawnArea[1]->z);
		$level = Server::getInstance()->getLevelByName($this->levelName);
		return new Position($x, $y, $z, $level);
	}
	public function getRandomGoldSpawnCoords() : Position{
		$x = mt_rand($this->goldArea[0]->x, $this->goldArea[1]->x);
		$y = mt_rand($this->goldArea[0]->y, $this->goldArea[1]->y);
		$z = mt_rand($this->goldArea[0]->z, $this->goldArea[1]->z);
		$level = Server::getInstance()->getLevelByName($this->levelName);
		return new Position($x, $y, $z, $level);
	}
	public function getName() : string{
		return $this->name;
	}
	public function getLevel() : string{
		return $this->levelName;
	}
	public function getPlayerSpawnA() : Position{
		return Position::fromObject($this->spawnArea[0],Server::getInstance()->getLevelByName($this->levelName));
	}
	public function getPlayerSpawnB() : Position{
		return Position::fromObject($this->spawnArea[1],Server::getInstance()->getLevelByName($this->levelName));
	}
	public function __toString() {
		return $this->getName();
	}
}