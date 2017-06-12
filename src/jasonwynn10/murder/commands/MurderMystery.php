<?php
namespace jasonwynn10\murder\commands;

use jasonwynn10\murder\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class MurderMystery extends PluginCommand {
	public function __construct(Main $plugin) {
		parent::__construct("murdermystery", $plugin);
		$this->setPermission("murder.mystery.play");
		$this->setDescription("Adds the player to the Murder Mystery Game Queue");
		$this->setAliases(["mm", "murder"]);
	}
	public function execute(CommandSender $sender, $alias, array $args) {
		if($sender instanceof Player and $this->testPermission($sender) and $this->getPlugin()->isEnabled()) {
			$this->getPlugin()->addQueue($sender);
		}
	}

    /**
     * @return Main
     */
	public function getPlugin() {
	    return parent::getPlugin();
    }
}
