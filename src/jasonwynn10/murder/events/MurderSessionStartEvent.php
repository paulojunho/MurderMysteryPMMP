<?php
namespace jasonwynn10\murder\events;

use jasonwynn10\murder\objects\MurderSession;
use pocketmine\plugin\Plugin;

class MurderSessionStartEvent extends MurderSessionEvent {
    public function __construct(Plugin $plugin, MurderSession $session) {
        parent::__construct($plugin, $session);
    }
}