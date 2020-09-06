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

namespace Bavfalcon9\Mavoric\Utils\Handlers\Pearl;

use pocketmine\Player;
use pocketmine\level\Position;

class PearlThrow {
    /** @var Player */
    private $player;
    /** @var int */
    private $thrownAt;
    /** @var int */
    private $completedAt;
    /** @var int */
    private $boundId;
    /** @var Position|Null */
    private $throwLocation;
    /** @var Position|Null */
    private $landingLocation;
    
    public function __construct(Player $player, int $boundId, int $thrown = null) {
        $this->player = $player;
        $this->thrownAt = $thrown ?? microtime(true);
        $this->completedAt = -1;
        $this->boundId = $boundId;
        $this->throwLocation = $player->getPosition();
        $this->landingLocation = null;
    }

    /**
     * Gets the player for the throw
     * @return Player - The player who threw the pearl
     */
    public function getPlayer(): ?Player {
        return $this->player;
    }

    /**
     * Gets the time taken for the pearl to land from being thrown.
     * @return int - time taken
     */
    public function getTimeCompleted(): int {
        if (!$this->isCompleted()) {
            return -1;
        } else {
            return $this->completedAt - $this->thrownAt;
        }
    }

    /** 
     * Gets the time the pearl landed at (-1 if still in air)
     * @return int - The time the pearl landed.
     */
    public function getLandingTime(): int {
        return $this->completedAt;
    }

    /**
     * Gets the location where the pearl landed.
     * @return Position|Null
     */
    public function getLandingLocation(): ?Position {
        return $this->landingLocation;
    }

    /**
     * Gets the location where the pearl landed.
     * @return Position|Null
     */
    public function getThrowLocation(): ?Position {
        return $this->throwLocation;
    }

    /**
     * Gets the time the pearl was thrown
     * @return int - The time the pearl was thrown
     */
    public function getThrownTime(): int {
        return $this->thrownAt;
    }

    /**
     * Gets whether the throw has been completed.
     * @return Bool - Whether or not the pearl has landed.
     */
    public function isCompleted(): Bool {
        return !($this->completedAt === -1);
    }

    /**
     * Sets the time that the pearl landed.
     * @param int $time - The time the pearl landed.
     * @param Position $position - Where the pearl landed
     * @return void
     */
    public function setCompleted(int $time, Position $position): void {
        $this->completedAt = $time;
        $this->landingLocation = $position;
        return;
    }

    /**
     * Gets the entity runtime id for the thrown pearl
     * @return int - the entity id
     */
    public function getPearlEntityId(): int {
        return $this->boundId;
    }
}