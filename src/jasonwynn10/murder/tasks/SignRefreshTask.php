<?php
namespace jasonwynn10\murder\tasks;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class SignRefreshTask extends PluginTask {
	public function __construct(Plugin $owner) {
		parent::__construct($owner);

	}
	public function onRun($currentTick) {
		// TODO: Implement onRun() method.
	}
}