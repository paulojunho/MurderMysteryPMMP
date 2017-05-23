<?php
namespace jasonwynn10\murder;

use pocketmine\command\PluginCommand;

class MurderMystery extends PluginCommand {
	public function __construct(Main $plugin) {
		parent::__construct("murdermystery", $plugin);
		$this->setPermission("murder.mystery.play");
		$this->setDescription("Adds the player to the Murder Mystery Game Queue");
		$this->setAliases(["mm", "murder"]);
	}
	public function execute(CommandSender $sender, $alias, array $args) {
		if($sender instanceof Player and $this->testPermission($sender)) {
			//TODO check args
		}
	}
}
