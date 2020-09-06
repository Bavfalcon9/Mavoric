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
use pocketmine\entity\Entity;
use pocketmine\event\Event;
use pocketmine\event\Cancellable;

class PearlThrownEvent extends Event implements Cancellable {
    /** @var Player */
    private $player;
    /** @var Entity */
    private $entity;
    /** @var int */
    private $id;

    public function __construct(Player $player, Entity $entity) {
        $this->player = $player;
        $this->entity = $entity;
        $this->id = $entity->getId();
    }

    /**
     * Get the player who threw the pearl
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * Get the pearl entity
     * @return Entity
     */
    public function getEntity(): Entity {
        return $this->entity;
    }

    /**
     * Get the entity id, this is here to handle, even if the entity is despawned.
     * @return int
     */
    public function getEntityId(): int {
        return $this->id;
    }
}