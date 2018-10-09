<?php
namespace jasonwynn10\murder\events;

use jasonwynn10\murder\objects\MurderSession;
use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\plugin\Plugin;

class MurderSessionEvent extends PluginEvent implements Cancellable {
	public static $handlerList = null;
	/** @var MurderSession $session */
	protected $session;

	public function __construct(Plugin $plugin, MurderSession $session) {
		parent::__construct($plugin);
		$this->session = $session;
	}

	public function getSession() : MurderSession {
		return $this->session;
	}

	public function setSession(MurderSession $session) : self {
		$this->session = $session;
		return $this;
	}
}