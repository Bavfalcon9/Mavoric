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

namespace Bavfalcon9\Mavoric\Utils;

use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;

class PlayerResolvable {
    /** @var Player|string|null */
    private $player;

    public function __construct($search) {
        $this->player = $search;
    }

    /**
     * Gets the player instance if exists
     * @return Player|null
     */
    public function getPlayer(): ?Player {
        $server = Server::getInstance();

        if ($this->search instanceof Player) {
            return $server->getPlayerExact($this->search->getName());
        }

        if (is_string($this->search)) {
            return $server->getPlayerExact($this->search);
        }

        return null;
    }
}