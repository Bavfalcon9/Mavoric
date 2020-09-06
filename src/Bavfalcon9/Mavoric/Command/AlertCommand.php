<?php

/**
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  @author Bavfalcon9
 *  @link https://github.com/Bavfalcon9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Command;

use pocketmine\plugin\Plugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use Bavfalcon9\Mavoric\Loader;
use Bavfalcon9\Mavoric\Mavoric;

class AlertCommand extends Command implements PluginIdentifiableCommand {
    /** @var Loader */
    private $plugin;
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Loader $plugin, Mavoric $mavoric) {
        parent::__construct("alerts");
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
        $this->description = "Toggle alerts.";
        $this->usageMessage = "/alerts <on/off/detailed>";
        $this->setAliases(['alert']);
        $this->setPermission("mavoric.alerts");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender->hasPermission('mavoric.alerts') && !$sender->isOp()) {
            $sender->sendMessage("§c§lError: §r§cYou are missing access to this command.");
            return true;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§c§lError: §r§cMissing argument one of: §7<on/off/detailed>");
            return true;
        }

        if ($args[0] === "off") {
            $this->mavoric->getVerboseNotifier()->addIgnored($sender->getName());
            $this->mavoric->getCheckNotifier()->addIgnored($sender->getName());
            $sender->sendMessage("§aYou disabled viewing of all alerts");
            return true;
        }

        if ($args[0] === "on") {
            $this->mavoric->getCheckNotifier()->removeIgnored($sender->getName());
            $sender->sendMessage("§aYou enabled viewing of alerts");
            return true;
        }

        if ($args[0] === "detailed") {
            $this->mavoric->getVerboseNotifier()->removeIgnored($sender->getName());
            $sender->sendMessage("§aYou enabled viewing of detailed alerts");
            return true;
        }

        $sender->sendMessage("§c§lError: §r§cInvalid argument, must be one of: §7on/off/detailed>");
        return true;
    }

    public function getPlugin(): Plugin {
        return $this->plugin;
    }
}