<?php
declare(strict_types=1);
namespace jasonwynn10\murder\tasks;

use jasonwynn10\murder\Main;
use jasonwynn10\murder\objects\MurderSession;
use pocketmine\scheduler\Task;

class CountdownTask extends Task {
	/** @var Main $plugin */
	protected $plugin;
	/** @var MurderSession $session */
	protected $session;

	public function __construct(Main $owner, MurderSession $session) {
		$this->plugin = $owner;
		$this->session = $session;
	}

	public function onRun($currentTick) {
		// TODO: Implement onRun() method.
	}
}