<?php
namespace jasonwynn10\murder\events;

use pocketmine\Player;
use pocketmine\plugin\Plugin;

use jasonwynn10\murder\objects\MurderSession;

class MurderSessionEndEvent extends MurderSessionEvent {
    /** @var string $winner */
    private $winner;
    public function __construct(Plugin $plugin, MurderSession $session, Player $winner){
        parent::__construct($plugin, $session);
        $this->winner = $winner->getName();
    }
    public function getWinner() : string{
        return $this->winner;
    }
    public function setWinner(string $winner) {
        $this->winner = $winner;
    }
}