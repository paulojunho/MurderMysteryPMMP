<?php
declare(strict_types=1);
namespace jasonwynn10\murder\commands;

use jasonwynn10\murder\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class MurderMystery extends PluginCommand {
	/** @var Main $plugin */
	private $plugin;
	public function __construct(Main $plugin) {
		parent::__construct("murdermystery", $plugin);
		$this->setPermission("murder.mystery.play");
		$this->setDescription("Adds the player to the Murder Mystery Game Queue");
		$this->setAliases(["mm", "murder"]);
	}

	/**
	 * @param CommandSender $sender
	 * @param string $alias
	 * @param array $args
	 *
	 * @return bool
	 */
	public function execute(CommandSender $sender, $alias, array $args) : bool {
		if($sender instanceof Player and $this->testPermission($sender) and $this->getPlugin()->isEnabled() and count($args) > 0) {
			if(isset($args[0])) {
				if(!array_search($args[0], $this->getPlugin()->getMaps())) {
					$sender->sendMessage($this->getPlugin()->getLanguage()->translateString("command.invalidMap", [$args[0]]));
				}
				if($this->plugin->inQueue($sender)) {
					$sender->sendMessage($this->plugin->getLanguage()->translateString("queue.alreadyjoined", [$args[0]]));
					return true;
				}
				$this->getPlugin()->addQueue($sender, $args[0]);
				$sender->sendMessage($this->plugin->getLanguage()->translateString("queue.joined", [$args[0]]));
			}else {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * @return Main
	 */
	public function getPlugin() : Plugin {
		return parent::getPlugin();
	}
}