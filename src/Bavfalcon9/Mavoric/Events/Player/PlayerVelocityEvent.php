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

namespace Bavfalcon9\Mavoric\Events\Player;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\Cancellable;

class PlayerVelocityEvent extends PlayerEvent implements Cancellable {
    /** @var Vector3 */
    private $velocity;
    /** @var float */
    private $horizontal;
    /** @var float */
    private $vertical;
    /** @var float */
    private $direction;

    public function __construct(Player $player, Vector3 $velocity, float $horizontal, float $vertical, float $direction) {
        $this->player = $player;
        $this->velocity = $velocity;
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
        $this->direction = $direction;
    }

    /**
     * @return Vector3
     */
    public function getVelocity(): Vector3 {
        return $this->velocity;
    }

    /**
     * @return float
     */
    public function getHorizontal(): float {
        return $this->horizontal;
    }

    /**
     * @return float
     */
    public function getVertical(): float {
        return $this->vertical;
    }

    /**
     * @return float
     */
    public function getDirection(): float {
        return $this->direction;
    }
}