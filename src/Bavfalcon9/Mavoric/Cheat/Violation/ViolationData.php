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

namespace Bavfalcon9\Mavoric\Cheat\Violation;

use pocketmine\Player;
use pocketmine\Server;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Events\Violation\ViolationChangeEvent;

class ViolationData {
    /** @var int[] */
    private $levels;
    /** @var Player */
    private $player;
    /** @var string */
    private $playerName;
    /** @var float */
    private $lastAddition;
    /** @var int|float */
    private $incrementsTime;
    /** @var int|float */
    private $incrementsLast;

    public function __construct(Player $player) {
        $this->levels = [];
        $this->player = $player;
        $this->playerName = $player->getName();
        $this->lastAddition = -1;
        $this->incrementsTime = time();
        $this->incrementsLast = 0;
    }

    /**
     * Increments the violation level for the given cheat or player
     * @param string $cheat - Cheat/Player to increment
     * @param int $amount - How much to increment
     * @return Int
     */
    public function incrementLevel(string $cheat, int $amount = 1): ?int {
        $this->updateIncrementTime();
        $ev = new ViolationChangeEvent($this->player, $cheat, $amount, $this->levels[$cheat] ?? 0, $this, true);
        $ev->call();

        if ($ev->isCancelled() === true) {
            return null;
        } else {
            if (!isset($this->levels[$cheat])) {
                $this->levels[$cheat] = 0;
            }
            $this->levels[$cheat] += $ev->getAmount();
            return $this->levels[$cheat];
        }
    }

    /** 
     * Deincrements a violation level
     * @param string $cheat - Cheat to deincrement
     * @param int $amount - How much to deincrement
     * @return int|null
     */
    public function deincrementLevel(string $cheat, int $amount = 1): ?int {
        $this->updateIncrementTime(-1);
        $ev = new ViolationChangeEvent($this->player, $cheat, $amount, $this->levels[$cheat] ?? 0, $this, false);
        $ev->call();

        if ($ev->isCancelled() === true) {
            return null;
        } else {
            if (!isset($this->levels[$cheat])) {
                $this->levels[$cheat] = 0;
            }
            $this->levels[$cheat] -= $ev->getAmount();
            return $this->levels[$cheat];
        }
    }

    /**
     * Gets the sum of all cheat violations
     * @return int
     */
    public function getViolationCountSum(): int {
        return array_sum(array_values($this->levels));
    }

    /**
     * Gets the highest level of violations for all cheats
     * @return int - Cheat number
     */
    public function getHighestLevelByCheat(): ?int {
        $highest = null;
        foreach ($this->levels as $cheat) {
            if ($highest === null || $highest < $cheat) {
                $highest = $cheat;
            }
        }

        return $highest;
    }

    /**
     * Gets the name of the cheat that has been most detected.
     * @return string|null - Cheat name.
     */
    public function getMostDetectedCheat(): ?string {
        $detected = null;
        foreach ($this->levels as $name => $level) {
            if ($detected === null || $detected[1] < $level) {
                $detected = [$name, $level];
            }
        }

        return $detected[0] ?? null;
    }

    /**
     * Gets a percentage of probability of cheating with the current data
     * @return float
     */
    public function getCheatProbability(): float {
        $amount = count($this->levels);
        $probable = $amount * 2;

        foreach ($this->levels as $cheat) {
            $probable += ($cheat / 2);
        }

        return ($probable >= 100) ? 100 : $probable;
    }

    /**
     * Gets the violation level for a given cheat or player
     */
    public function getLevel(string $cheat): ?int {
        return (!isset($this->levels[$cheat])) ? null : $this->levels[$cheat];
    }
    
    /**
     * Gets the violation map for all cheats
     * @return int[]
     */
    public function getLevels(): array {
        return $this->levels;
    }

    /**
     * Gets the last time a violation tick was added to the given data
     * @return int
     */
    public function getLastAdditionTime(): int {
        return $this->lastAddition;
    }

    /**
     * Gets the difference from when last time a violation tick was added to now.
     * @return float
     */
    public function getLastAdditionFromNow(): float {
        return (microtime(true) - $this->lastAddition);
    }

    /**
     * Clears the violation data
     * @return int[]
     */
    public function clear(): array {
        return $this->levels = [];
    }

    /**
     * Forces the player variable to be updated.
     */
    public function forceUpdateStoredPlayer(): ViolationData {
        $server = Server::getInstance();
        $player = $server->getPlayerExact($this->playerName);

        if ($player !== null) {
            $this->player = $player;
        }

        return $this;
    }

    /**
     * Gets the increments in the amount of past second
     */
    public function getIncrementsInPastSecond(): int {
        return $this->incrementsLast;
    }

    /**
     * Updates the last addition time
     */
    private function updateIncrementTime(float $amt = 1): void {
        $this->lastAddition = \microtime(true);
        if ($this->incrementsTime + 1 <= \microtime(true)) {
            $this->incrementsLast = 0;
            $this->incrementsTime = \microtime(true);
        } else {
            $this->incrementsLast += $amt;
        }
    }
}