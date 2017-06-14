<?php
namespace jasonwynn10\murder;

use pocketmine\lang\BaseLang;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

use jasonwynn10\murder\objects\GameMap;
use jasonwynn10\murder\commands\MurderMystery;
use jasonwynn10\murder\objects\MurderSession;
use jasonwynn10\murder\events\MurderListener;

use spoondetector\SpoonDetector;

class Main extends PluginBase {
	/** @var BaseLang $baseLang */
	private $baseLang = null;
	/** @var string[][] $queue */
	private $queue = [];
	/** @var MurderSession[] $sessions */
	private $sessions = [];
	/** @var Config $signConfig */
	private $signConfig;
	/** @var Sign[] $signTiles */
	private $signTiles = [];
	/** @var string[] $maps */
	private $maps = [];
	public function onLoad() {
		$this->getServer()->getCommandMap()->register(MurderMystery::class, new MurderMystery($this));
		$this->getServer()->getPluginManager()->registerEvents(new MurderListener($this), $this);
	}
	public function onEnable() {
		SpoonDetector::printSpoon($this,"spoon.txt");
		$this->saveDefaultConfig();
		$lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
		$this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");
		$this->signConfig = new Config($this->getDataFolder()."signs.yml",CONFIG::YAML);
		$this->loadMaps();
		$this->loadSigns();
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}

	/**
	 * @return bool
	 */
	public static function isDev() {
		return self::isPhar();
	}

	private function loadMaps() {
		foreach ($this->getConfig()->get("Games",[]) as $name => $data) {
			if(!isset($data["World"]) or !$this->getServer()->loadLevel($data["World"])) {
				if(self::isDev()) {
					$this->getLogger()->info("Map '".$name."' failed to load with invalid Level");
				}
				continue;
			}
			if(!isset($data["Player-Spawn-Area"]) or !is_array($data["Player-Spawn-Area"])) {
				if(self::isDev()) {
					$this->getLogger()->info("Map '".$name."' failed to load with invalid Player Spawn Coordinates");
				}
				continue;
			}
			if(!isset($data["Player-Spawn-Area"]["X1"]) or !isset($data["Player-Spawn-Area"]["Y1"]) or !isset($data["Player-Spawn-Area"]["Z1"]) or !isset($data["Player-Spawn-Area"]["X2"]) or !isset($data["Player-Spawn-Area"]["Y2"]) or !isset($data["Player-Spawn-Area"]["Z2"])) {
				if(self::isDev()) {
					$this->getLogger()->info("Map '".$name."' failed to load with invalid Player Spawn Coordinates");
				}
				continue;
			}
			if(!isset($data["PGold-Spawn-Area"]) or !is_array($data["Gold-Spawn-Area"])) {
				if(self::isDev()) {
					$this->getLogger()->info("Map '".$name."' failed to load with invalid Gold Spawn Coordinates");
				}
				continue;
			}
			if(!isset($data["Gold-Spawn-Area"]["X1"]) or !isset($data["Gold-Spawn-Area"]["Y1"]) or !isset($data["Gold-Spawn-Area"]["Z1"]) or !isset($data["Gold-Spawn-Area"]["X2"]) or !isset($data["Gold-Spawn-Area"]["Y2"]) or !isset($data["Gold-Spawn-Area"]["Z2"])) {
				if(self::isDev()) {
					$this->getLogger()->info("Map '".$name."' failed to load with invalid Gold Spawn Coordinates");
				}
				continue;
			}
			$map = new GameMap($name, new Vector3(), new Vector3(), new Vector3(), new Vector3(), $data["World"]); //TODO use set variables
			$this->maps[$name] = $map;
			if(self::isDev()) {
				$this->getLogger()->info("Map '".$map."' Loaded with Level '". $map->getLevel()."'");
			}
		}
	}

	private function loadSigns() {
		foreach ($this->getSignConfig()->getAll() as $signs => $data) {
			if(!($world = $this->getServer()->getLevelByName($data["world"])) instanceof Level) {
				continue;
			}
			$pos = new Vector3($data["x"],$data["y"],$data["z"]);
			if(!($signTile = $world->getTile($pos)) instanceof Sign) {
				if(self::isDev()) {
					$this->getLogger()->info("Sign failed to load! $signTile");
				}
				continue;
			}
			$this->signTiles[] = $signTile;
			if(self::isDev()) {
				$this->getLogger()->info("Sign loaded! $signTile");
			}
		}
	}

	public function getSignConfig() : Config{
		return $this->signConfig;
	}

	// API
	
	/**
	 * @api
	 * @return BaseLang
	 */
	public function getLanguage() : BaseLang{
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
	 *
	 */
	public function removeQueue(Player $player) {
		foreach($this->queue as $map => $players) {
			if(in_array($player->getName(),$players)){
				$key = array_search($player->getName(),$players);
				unset($this->queue[$key]);
				return;
			}
		}
	}

	/**
	 * @api
	 *
	 * @param Player $player
	 * @return bool
	 */
	public function inQueue(Player $player) {
		foreach($this->queue as $map => $players) {
			if(in_array($player->getName(),$players)){
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
	public function addSign(Sign $signTile){
		$signs = $this->getSignConfig()->get("signs",[]);
		$signs[count($this->signTiles)] = [$signTile->getX(), $signTile->getY(), $signTile->getZ(), $signTile->getLevel()->getName()];
		$this->getSignConfig()->set("signs", $signs);
		$this->getSignConfig()->save();
		$this->signTiles[] = $signTile;
		if(self::isDev()) {
			$this->getLogger()->info("New Sign Created! $signTile");
		}
	}

	/**
	 * @api
	 *
	 * @return int
	 */
	public function getNumberOfFreeArenas(){
		$numberOfFreeArenas = count($this->sessions);
		foreach ($this->sessions as $session){
			if($session->isActive()){
				$numberOfFreeArenas--;
			}
		}
		return $numberOfFreeArenas;
	}

	/**
	 * @api
	 *
	 * @param string|Player $player
	 * @return bool|MurderSession
	 */
	public function inSession($player) {
		$player = $player instanceof Player ? $player->getName() : $player;
		foreach($this->sessions as $session) {
			if(in_array($player,$session->getPlayers())) {
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
	public function getMaps() {
		return $this->maps;
	}
}