<?php
namespace jasonwynn10\murder\tasks;

use pocketmine\scheduler\PluginTask;

use jasonwynn10\murder\Main;
use jasonwynn10\murder\MurderSession;

class CountdownTask extends PluginTask {
    /** @var MurderSession $session */
    private $session;
    public function __construct(Main $owner, MurderSession $session) {
        parent::__construct($owner);
        $this->session = $session;
    }
    public function onRun($currentTick) {
        // TODO: Implement onRun() method.
    }
}