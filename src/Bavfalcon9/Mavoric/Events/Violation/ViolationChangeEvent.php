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

namespace Bavfalcon9\Mavoric\Events\Violation;

use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use pocketmine\Player;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Violation\ViolationData;

/**
 * Called when a violation is either removed or added to a player.
 */
class ViolationChangeEvent extends Event implements Cancellable {
    /** @var Player */
    private $player;
    /** @var string */
    private $cheat;
    /** @var int */
    private $amount;
    /** @var int */
    private $current;
    /** @var ViolationData */
    private $vData;
    /** @var bool */
    private $increased;

    public function __construct(Player $player, string $cheat, int $amount, int $current, ViolationData $vData, bool $increased = true) {
        $this->player = $player;
        $this->cheat = $cheat;
        $this->amount = $amount;
        $this->current = $current;
        $this->vData = $vData;
        $this->increased = $increased;
    }

    /**
     * Gets the player that had the violation change.
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * Gets the cheat string changed
     * @return string
     */
    public function getCheat(): string {
        return $this->cheat;
    }

    /**
     * Gets the total amount that the violation changed.
     * @return int
     */
    public function getAmount(): int {
        return $this->amount;
    }

    /**
     * Gets the total amount of violations before the current one has been added
     * @return int
     */
    public function getCurrent(): int {
        return $this->current;
    }

    /**
     * Gets the violation data class
     * @return ViolationData
     */
    public function getViolation(): ViolationData {
        return $this->vData;
    }

    /**
     * Gets the total amount that the violation changed.
     * @return bool
     */
    public function isIncreased(): bool {
        return $this->increased;
    }

    /**
     * Sets the total amount the violation changed.
     * @return int
     */
    public function setAmount(int $amount): int {
        return $this->amount = $amount;
    }
} 