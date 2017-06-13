<?php
namespace jasonwynn10\murder;

use pocketmine\lang\BaseLang;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as TF;

use jasonwynn10\murder\commands\MurderMystery;
use jasonwynn10\murder\events\MurderListener;

use spoondetector\SpoonDetector;

class Main extends PluginBase {
	/** @var BaseLang $baseLang */
	private $baseLang = null;
	/** @var string[] $queue */
	private $queue = [];
	/** @var MurderSession[] $sessions */
	private $sessions = [];
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
		$this->getLogger()->notice(TF::GREEN."Enabled!");
	}
	public function onDisable() {
		$this->getLogger()->notice(TF::GREEN."Disabled!");
	}
	public static function isDev() {
	    return self::isPhar();
    }

	// API
	
	/**
	 * @api
	 * @return BaseLang
	 */
	public function getLanguage() : BaseLang {
		return $this->baseLang;
	}
	/**
	 * @api
     *
     * @param Player $player
     *
	 */
	public function addQueue(Player $player) {
		$this->queue[] = $player->getName();
	}
	/**
	 * @api
     *
     * @param Player $player
     *
	 */
	public function removeQueue(Player $player) {
		if(in_array($player->getName(),$this->queue)){
		    $key = array_search($player->getName(),$this->queue);
            unset($this->queue[$key]);
        }
	}

    public function addSign(Sign $signTile){
        $signs = $this->getConfig()->get("signs",[]);
        $signs[count($this->signTiles)] = [$signTile->getX(), $signTile->getY(), $signTile->getZ(), $signTile->getLevel()->getName()];
        $this->getConfig()->set("signs", $signs);
        $this->getConfig()->save();
        array_push($this->signTiles, $signTile);
    }

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
	 * @var string|Player $player
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

	public function getMaps() {
	    return $this->maps;
    }

}
