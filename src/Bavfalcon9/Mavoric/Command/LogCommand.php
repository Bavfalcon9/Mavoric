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
use Bavfalcon9\Mavoric\Cheat\Violation\ViolationData;

class LogCommand extends Command implements PluginIdentifiableCommand {
    /** @var Loader */
    private $plugin;
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Loader $plugin, Mavoric $mavoric) {
        parent::__construct("logs");
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
        $this->description = "Manage Logs for a user.";
        $this->usageMessage = "/logs <player> <view/confirm/deny> [cheat/all]";
        $this->setAliases(['mlogs']);
        $this->setPermission("mavoric.alerts");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender->hasPermission('mavoric.alerts') && !$sender->isOp()) {
            $sender->sendMessage("§c§lError: §r§cYou are missing access to this command.");
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§c§lError: §r§cMissing argument one: §7<player>");
            return;
        }

        if (!isset($args[1])) {
            $args[1] = 'view';
        }

        $data = $this->mavoric->getViolationDataFor($args[0]);

        if ($data === null) {
            $sender->sendMessage("§c§lError: §r§cPoayer provided has no logs.");
            return;
        }

        if ($data->getViolationCountSum() <= 0) {
            $sender->sendMessage("§c§lError: §r§cPlayer provided has no logs.");
            return;
        }

        if ($args[1] === 'view' || $args[1] === 'info') {
            $cheats = $data->getLevels();
            $neatlyMapped = []; // array_map does not pass keys ._.

            foreach ($cheats as $name => $level) {
                $neatlyMapped[] = "§c$name" . "§8:§4 ". $level;
            }

            $sender->sendMessage("§8======= §cLogs §8=======");
            $sender->sendMessage("§cPlayer§8: §4" . $args[0]);
            $sender->sendMessage("§cTotal Alerts§8: §4" . $data->getViolationCountSum());
            $sender->sendMessage("§cMost Detected§8: §4" . ($data->getMostDetectedCheat() ?? 'None'));
            $sender->sendMessage("§cProbability§8: §4" . $data->getCheatProbability() . "%");
            $sender->sendMessage("§cIs Cheating§8: " . (($data->getViolationCountSum() >= 50) ? "§aYes" : "§4No"));
            $sender->sendMessage("§cAlert info§8: §8(" . implode(', ', $neatlyMapped) . "§8)");
            $sender->sendMessage("§8===================");
            return;
        }

        if ($args[1] === 'confirm') {
            $type = $args[2] ?? 'all';

            if ($type === 'all') {
                $this->mavoric->getCheckNotifier()->notify('§a' . $sender->getName() . " has confirmed all alerts for: $args[0]");
                $sender->sendMessage('§aConfirmed all alerts for: '. $args[0]);
                $this->issueBan($args[0], $data, '+7 Day');
            } else {
                $level = $data->getLevel($type);
                if (!$level) {
                    $sender->sendMessage("§c§lError: §r§cPlayer does not have any alerts for: $type");
                    return;
                } else {
                    $this->mavoric->getCheckNotifier()->notify('§a' . $sender->getName() . " has confirmed all $type alerts for: $args[0]");
                    $sender->sendMessage('§aConfirmed all alerts for: '. $args[0]);
                }
            }
            return;
        }

        if ($args[1] === 'deny') {
            $type = $args[2] ?? 'all';

            if ($type === 'all') {
                $this->mavoric->getViolationDataFor($args[0])->clear();
                $this->mavoric->getCheckNotifier()->notify('§a' . $sender->getName() . " has denied all alerts for: $args[0]");
                $sender->sendMessage('§aDenied all alerts for: '. $args[0]);
            } else {
                $level = $data->getLevel($type);
                if (!$level) {
                    $sender->sendMessage("§c§lError: §r§cPlayer does not have any alerts for: $type");
                    return;
                } else {
                    $this->mavoric->getViolationDataFor($args[0])->deincrementLevel($type, $data->getLevel($type));
                    $this->mavoric->getCheckNotifier()->notify('§a' . $sender->getName() . " has denied all $type alerts for: $args[0]");
                    $sender->sendMessage('§aDenied all alerts for: '. $args[0]);
                }
            }
            return;
        }

        $sender->sendMessage("§c§lUsage: §r§c$this->usageMessage");
        return;
    }

    public function getPlugin(): Plugin {
        return $this->plugin;
    }

    protected function issueBan(string $player, ViolationData $data, string $amt): void {
        $banList = $this->plugin->getServer()->getNameBans();
        $banList->addBan($player, '§4[Mavoric] Cheating [VC: ' . $data->getViolationCountSum() . ']', new \DateTime("+7 Day"), 'Mavoric');
        if ($player = $this->plugin->getServer()->getPlayerExact($player)) {
            $player->close('', '§4[Mavoric] Cheating [VC: ' . $data->getViolationCountSum() . ']');
            return;
        }
    }
}