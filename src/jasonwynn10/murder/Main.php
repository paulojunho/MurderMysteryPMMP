<?php
declare(strict_types=1);
namespace jasonwynn10\murder;

use jasonwynn10\murder\commands\MurderMystery;
use jasonwynn10\murder\events\MurderListener;
use jasonwynn10\murder\objects\GameMap;
use jasonwynn10\murder\objects\MurderSession;
use pocketmine\lang\BaseLang;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use spoondetector\SpoonDetector;

class Main extends PluginBase {
	/** @var BaseLang $baseLang */
	protected $baseLang = null;
	/** @var string[][] $queue */
	protected $queue = [];
	/** @var MurderSession[] $sessions */
	protected $sessions = [];
	/** @var Config $signConfig */
	protected $signConfig;
	/** @var Sign[] $signTiles */
	protected $signTiles = [];
	/** @var GameMap[] $maps */
	protected $maps = [];

	public function onLoad() : void {
		$this->getServer()->getCommandMap()->register("MurderMystery", new MurderMystery($this));
	}

	public function onEnable() : void {
		SpoonDetector::printSpoon($this, "spoon.txt");
		if($this->isDisabled())
			return;
		$this->getServer()->getPluginManager()->registerEvents(new MurderListener($this), $this);
		$this->saveDefaultConfig();
		/** @var string $lang */
		$lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
		$this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");
		$this->signConfig = new Config($this->getDataFolder() . "signs.yml", CONFIG::YAML);
		$this->reloadMaps();
		$this->reloadSigns();
		$this->getLogger()->notice(TF::GREEN . "Enabled!");
	}

	public function reloadMaps() {
		$this->maps = [];
		foreach($this->getConfig()->get("Games", []) as $name => $data) {
			if(!isset($data["World"]) or !$this->getServer()->loadLevel($data["World"])) {
				$this->getLogger()->debug("Map '" . $name . "' failed to load with invalid Level");
				continue;
			}
			if(!isset($data["Player-Spawn-Area"]) or !is_array($data["Player-Spawn-Area"])) {
				$this->getLogger()->debug("Map '" . $name . "' failed to load with invalid Player Spawn Coordinates");
				continue;
			}
			if(!isset($data["Player-Spawn-Area"]["X1"]) or !isset($data["Player-Spawn-Area"]["Y1"]) or !isset($data["Player-Spawn-Area"]["Z1"]) or !isset($data["Player-Spawn-Area"]["X2"]) or !isset($data["Player-Spawn-Area"]["Y2"]) or !isset($data["Player-Spawn-Area"]["Z2"])) {
				$this->getLogger()->debug("Map '" . $name . "' failed to load with invalid Player Spawn Coordinates");
				continue;
			}
			if(!isset($data["PGold-Spawn-Area"]) or !is_array($data["Gold-Spawn-Area"])) {
				$this->getLogger()->debug("Map '" . $name . "' failed to load with invalid Gold Spawn Coordinates");
				continue;
			}
			if(!isset($data["Gold-Spawn-Area"]["X1"]) or !isset($data["Gold-Spawn-Area"]["Y1"]) or !isset($data["Gold-Spawn-Area"]["Z1"]) or !isset($data["Gold-Spawn-Area"]["X2"]) or !isset($data["Gold-Spawn-Area"]["Y2"]) or !isset($data["Gold-Spawn-Area"]["Z2"])) {
				$this->getLogger()->debug("Map '" . $name . "' failed to load with invalid Gold Spawn Coordinates");
				continue;
			}
			$map = new GameMap($name, new Vector3((float) $data["Player-Spawn-Area"]["X1"], (float) $data["Player-Spawn-Area"]["Y1"], (float) $data["Player-Spawn-Area"]["Z1"]), new Vector3((float) $data["Player-Spawn-Area"]["X2"], (float) $data["Player-Spawn-Area"]["Y2"], (float) $data["Player-Spawn-Area"]["Z2"]), new Vector3((float) $data["Gold-Spawn-Area"]["X1"], (float) $data["Gold-Spawn-Area"]["Y1"], (float) $data["Gold-Spawn-Area"]["Z1"]), new Vector3((float) $data["Gold-Spawn-Area"]["X2"], (float) $data["Gold-Spawn-Area"]["Y2"], (float) $data["Gold-Spawn-Area"]["Z2"]), $data["World"], $data["Max-Players"], $data["Min-Players"]);
			$this->maps[$name] = $map;
			$this->getLogger()->debug("Map '" . $map . "' Loaded with Level '" . $map->getLevel() . "'");
		}
	}

	public function reloadSigns() {
		$this->signTiles = [];
		foreach($this->signConfig->getAll() as $signs => $data) {
			if(!($world = $this->getServer()->getLevelByName($data["world"])) instanceof Level) {
				continue;
			}
			$pos = new Vector3($data["x"], $data["y"], $data["z"]);
			$signTile = $world->getTile($pos);
			if(!$signTile instanceof Sign or $signTile->getLine(0) === TextFormat::RESET . TextFormat::GREEN . "Murder Mystery") {
				$this->getLogger()->debug("Sign failed to load! $signTile");
				continue;
			}
			$this->signTiles[] = $signTile;
			$this->getLogger()->debug("Sign loaded! $signTile");
		}
	}

	/**
	 * @return Config
	 */
	public function getSignConfig() : Config {
		return $this->signConfig;
	}

	public function onDisable() : void {
		$this->getLogger()->notice(TF::GREEN . "Disabled!");
	}

	/**
	 * @api
	 *
	 * @return BaseLang
	 */
	public function getLanguage() : BaseLang {
		return $this->baseLang;
	}

	/**
	 * @api
	 *
	 * @param Player $player
	 * @param string $map
	 */
	public function addQueue(Player $player, string $map) {
		$this->queue[$map][] = $player->getName();
	}

	/**
	 * @api
	 *
	 * @param Player $player
	 * @param null|string $map
	 */
	public function removeQueue(Player $player, ?string $map = null) {
		if(isset($map) and in_array($map, array_keys($this->queue))) {
			if(in_array($player->getName(), $this->queue[$map])) {
				$key = array_search($player->getName(), $this->queue[$map]);
				unset($this->queue[$map][$key]);
			}
			return;
		}
		foreach($this->queue as $map => $players) {
			if(in_array($player->getName(), $players)) {
				$key = array_search($player->getName(), $players);
				unset($this->queue[$map][$key]);
				return;
			}
		}
	}

	/**
	 * @api
	 *
	 * @param Player $player
	 *
	 * @param null|string $map
	 *
	 * @return bool
	 */
	public function inQueue(Player $player, ?string $map = null) : bool {
		if(isset($map) and in_array($map, array_keys($this->queue))) {
			return in_array($player->getName(), $this->queue[$map]);
		}
		foreach($this->queue as $map => $players) {
			if(in_array($player->getName(), $players)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @api
	 *
	 * @param Sign $signTile
	 */
	public function addSign(Sign $signTile) {
		$signs = $this->signConfig->get("signs", []);
		$signs[count($this->signTiles)] = [
			$signTile->x,
			$signTile->y,
			$signTile->z,
			$signTile->getLevel()->getName()
		];
		$this->signConfig->set("signs", $signs);
		$this->signConfig->save();
		$this->signTiles[] = $signTile;
		$this->getLogger()->debug("New Sign Created! $signTile");
	}

	/**
	 * @api
	 *
	 * @return int
	 */
	public function getNumberOfFreeArenas() : int {
		$numberOfFreeArenas = count($this->sessions);
		foreach($this->sessions as $session) {
			if($session->isActive()) {
				$numberOfFreeArenas--;
			}
		}
		return $numberOfFreeArenas;
	}

	/**
	 * @api
	 *
	 * @param string|Player $player
	 *
	 * @return bool|MurderSession
	 */
	public function inSession($player) {
		$player = $player instanceof Player ? $player->getName() : $player;
		foreach($this->sessions as $session) {
			if(in_array($player, $session->getPlayers())) {
				return $session;
			}
		}
		return false;
	}

	/**
	 * @api
	 *
	 * @return string[]
	 */
	public function getMaps() : array {
		return $this->maps;
	}

	/**
	 * @return Sign[]
	 */
	public function getSigns() : array {
		return $this->signTiles;
	}

	/**
	 * @return MurderSession[]
	 */
	public function getSessions() : array {
		return $this->sessions;
	}

	/**
	 * @return string[][]
	 */
	public function getQueue() : array {
		return $this->queue;
	}

	/**
	 * @param string $map
	 *
	 * @return null|MurderSession
	 */
	public function startSession(string $map) : ?MurderSession {
		if(isset($this->queue[$map]) and isset($this->maps[$map])) {
			$players = $this->queue[$map];
			$key = array_rand($players);
			$detective = $players[$key];
			unset($players[$key]);
			$key = array_rand($players);
			$killer = $players[$key];
			unset($players[$key]);
			$innocent = $players;
			$map = $this->maps[$map];
			return $this->sessions[$map->getName()] = new MurderSession($map->getName(), $map, $killer, $detective, ...$innocent);
		}
		return null;
	}

	/**
	 * @param string $map
	 *
	 * @return null|MurderSession
	 */
	public function getMapSession(string $map) : ?MurderSession {
		if(!in_array($map, array_keys($this->sessions))) {
			return null;
		}
		return $this->sessions[$map];
	}

	/**
	 * @param string $map
	 *
	 * @return string[]
	 */
	public function getMapQueue(string $map) : array {
		if(!in_array($map, array_keys($this->queue))) {
			return [];
		}
		return $this->queue[$map];
	}
}