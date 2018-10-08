<?php
declare(strict_types=1);
namespace jasonwynn10\murder\tasks;

use jasonwynn10\murder\Main;
use pocketmine\scheduler\Task;

class SignRefreshTask extends Task {
	/** @var Main $plugin */
	private $plugin;

	public function __construct(Main $owner) {
		$this->plugin = $owner;
	}

	public function onRun($currentTick) {
		// TODO: Implement onRun() method.
	}
}