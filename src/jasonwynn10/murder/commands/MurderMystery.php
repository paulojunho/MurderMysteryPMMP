<?php
namespace jasonwynn10\murder\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

use jasonwynn10\murder\Main;

class MurderMystery extends PluginCommand {
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
	public function execute(CommandSender $sender, $alias, array $args) {
		if($sender instanceof Player and $this->testPermission($sender) and $this->getPlugin()->isEnabled() and count($args) > 0) {
		    if(isset($args[0]) and array_search($args[0],$this->getPlugin()->getMaps())) {
                $this->getPlugin()->addQueue($sender,$args[0]);
            }elseif(isset($args[0])) {
		        $sender->sendMessage($this->getPlugin()->getLanguage()->translateString("command.invalidMap",[$args[0]]));
            }else{
		        return false;
            }
			return true;
		}
		return false;
	}

    /**
     * @return Main|\pocketmine\plugin\Plugin
     */
	public function getPlugin() {
	    return parent::getPlugin();
    }
}
