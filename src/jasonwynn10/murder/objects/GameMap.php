<?php
namespace jasonwynn10\murder\objects;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

class GameMap {
	/** @var string $name */
	protected $name;
	/** @var string $levelName */
	protected $levelName;
	/** @var Vector3[] $spawnArea */
	protected $spawnArea = [];
	/** @var Vector3[] $goldArea */
	protected $goldArea = [];
	/** @var int $minimumPlayers */
	protected $minimumPlayers = 4;
	/** @var int $maximumPlayers */
	protected $maximumPlayers = 7;

	/**
	 * GameMap constructor.
	 *
	 * @param string $name
	 * @param Vector3 $playerSpawnA
	 * @param Vector3 $playerSpawnB
	 * @param Vector3 $goldSpawnA
	 * @param Vector3 $goldSpawnB
	 * @param string $levelName
	 * @param int $minimumPlayers
	 * @param int $maximumPlayers
	 */
	public function __construct(string $name, Vector3 $playerSpawnA, Vector3 $playerSpawnB, Vector3 $goldSpawnA, Vector3 $goldSpawnB, string $levelName, int $minimumPlayers, int $maximumPlayers) {
		$this->spawnArea[0] = $playerSpawnA;
		$this->spawnArea[1] = $playerSpawnB;
		$this->goldArea[0] = $goldSpawnA;
		$this->goldArea[1] = $goldSpawnB;
		$this->levelName = $levelName;
		$this->name = $name;
		$this->minimumPlayers = $minimumPlayers;
		$this->maximumPlayers = $maximumPlayers;
	}

	/**
	 * @return Position
	 */
	public function getRandomPlayerSpawnCoords() : Position {
		$x = $y = $z = 0;
		$level = null;
		while(true) {
			$x = mt_rand($this->spawnArea[0]->x, $this->spawnArea[1]->x);
			$y = mt_rand($this->spawnArea[0]->y, $this->spawnArea[1]->y);
			$z = mt_rand($this->spawnArea[0]->z, $this->spawnArea[1]->z);
			$level = Server::getInstance()->getLevelByName($this->levelName);
			if($level !== null and $level->getBlockIdAt($x, $y, $z) === Block::AIR and $level->getBlockIdAt($x, $y + 1, $z) === Block::AIR) {
				break;
			}
		}
		return new Position($x, $y, $z, $level);
	}

	/**
	 * @return Position
	 */
	public function getRandomGoldSpawnCoords() : Position {
		$x = $y = $z = 0;
		$level = null;
		while(true) {
			$x = mt_rand($this->goldArea[0]->x, $this->goldArea[1]->x);
			$y = mt_rand($this->goldArea[0]->y, $this->goldArea[1]->y);
			$z = mt_rand($this->goldArea[0]->z, $this->goldArea[1]->z);
			$level = Server::getInstance()->getLevelByName($this->levelName);
			if($level !== null and $level->getBlockIdAt($x, $y, $z) === Block::AIR and $level->getBlockIdAt($x, $y + 1, $z) === Block::AIR) {
				break;
			}
		}
		return new Position($x, $y, $z, $level);
	}

	/**
	 * @return string
	 */
	public function getLevel() : string {
		return $this->levelName;
	}

	/**
	 * @return Position
	 */
	public function getPlayerSpawnA() : Position {
		return Position::fromObject($this->spawnArea[0], Server::getInstance()->getLevelByName($this->levelName));
	}

	/**
	 * @return Position
	 */
	public function getPlayerSpawnB() : Position {
		return Position::fromObject($this->spawnArea[1], Server::getInstance()->getLevelByName($this->levelName));
	}

	/**
	 * @return string
	 */
	public function __toString() : string {
		return $this->getName();
	}

	/**
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * @param int $minimumPlayers
	 *
	 * @return GameMap
	 */
	public function setMinimumPlayers(int $minimumPlayers) : self {
		$this->minimumPlayers = $minimumPlayers;
		return $this;
}

	/**
	 * @param int $maximumPlayers
	 *
	 * @return GameMap
	 */
	public function setMaximumPlayers(int $maximumPlayers) : self {
		$this->maximumPlayers = $maximumPlayers;
		return $this;
}

	/**
	 * @return int
	 */
	public function getMaximumPlayers() : int {
		return $this->maximumPlayers;
	}

	/**
	 * @return int
	 */
	public function getMinimumPlayers() : int {
		return $this->minimumPlayers;
	}
}