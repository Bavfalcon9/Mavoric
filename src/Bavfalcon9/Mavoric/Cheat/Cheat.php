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

namespace Bavfalcon9\Mavoric\Cheat;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use Bavfalcon9\Mavoric\Mavoric;

class Cheat implements Listener {
    /** @var Mavoric */
    protected $mavoric;
    /** @var int[] */
    protected $violations;
    /** @var string */
    private $name;
    /** @var string */
    private $module;
    /** @var int */
    private $id;
    /** @var bool */
    private $enabled;
    /** @var string[] */
    private static $registered = [];

    public function __construct(Mavoric $mavoric, string $name, string $module, int $id, bool $enabled = true) {
        $this->mavoric = $mavoric;
        $this->name = $name;
        $this->module = $module;
        $this->id = $id;
        $this->enabled = $enabled;
        $this->violations = [];
        self::$registered[] = $name;
    }

    /**
     * Gets the cheat name
     * @return String
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Gets the module of cheat
     * @return String
     */
    public function getModule(): string {
        return $this->module;
    }

    /**
     * Get the id of the cheat
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Whether or not the cheat is enabled
     * @return Bool
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * Whether or not the cheat is enabled
     * @return Bool
     */
    protected function setEnabled(bool $val): bool {
        return $this->enabled = $val;
    }

    /**
     * @param string $name - Name to increment
     * @param int $amount - Amount to increment
     */
    public function increment(string $name, int $amount = 1): int {
        if (!isset($this->violations[$name])) {
            $this->violations[$name] = 0;
        }

        return $this->violations[$name] += $amount;
    }

    /**
     * @param string $name - Name to deincrement
     * @param int $amount - Amount to deincrement
     */
    public function deincrement(string $name, int $amount = 1): int {
        if (!isset($this->violations[$name])) {
            $this->violations[$name] = 0;
        }

        return $this->violations[$name] -= $amount;
    }

    /**
     * @param string $name - Name to get
     */
    public function getViolation(string $name): int {
        if (!isset($this->violations[$name])) {
            $this->violations[$name] = 0;
        }

        return $this->violations[$name];
    }

    /**
     * Notifies the verbose notifier and increments the specified players data
     * @param int $remainder - Remainder to notify at (IF condition is met), -1 for non
     * @param Player $player - Player to increment the level for
     * @param string[] $verboseData - Array of Verbose Indexes to append to default alert string
     * 
     * @return void
     */
    public function notifyAndIncrement(Player $player, int $remainder, int $increment, array $verboseData = []): void {
        if (($remainder !== -1) && ($this->getViolation($player->getName()) % $remainder === 0) === false) return;
        $msg = "§4[AC]: §c{$player->getName()} §7failed §c{$this->module}[{$this->name}-{$this->id}]";
        $verboseMsg = '§8(';
        $i = 0;
        foreach ($verboseData as $name => $value) {
            if (sizeof($verboseData) - 1 === $i) {
                $verboseMsg .= "§7{$name}-§b{$value}";
            } else {
                $verboseMsg .= "§7{$name}-§b{$value}" . '§7, ';
                $i++;
            }
        }

        $violations = $this->mavoric->getViolationDataFor($player->getName());
        $violations->incrementLevel($this->getName(), $increment);
        $verboseMsg .= '§8)§c ' . $violations->getCheatProbability() . '%';
        $notifier = $this->mavoric->getVerboseNotifier();
        $notifier->notify($msg, $verboseMsg);
        $this->debug($msg . ' ' . $verboseMsg);
        return;
    }

    /**
     * @return bool
     */
    public function isSuppressed(): bool {
        // To Do: Check config
        return true;
    }

    /**
     * Get the server instance from the cheat
     * @return Server
     */
    public function getServer(): Server {
        return Server::getInstance();
    }

    /**
     * Debug a cheat kek
     * @param string $message
     */
    protected function debug(string $message): void {
        $this->getServer()->getLogger()->debug('[MAVORIC/' . $this->getName() . "-" . $this->getId() . "] " . $message);
        return;
    }
}