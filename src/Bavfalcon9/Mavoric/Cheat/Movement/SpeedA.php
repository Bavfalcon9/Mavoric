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

namespace Bavfalcon9\Mavoric\Cheat\Movement;

use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class SpeedA extends Cheat {
    /** @var int[] */
    protected $lastMovements;

    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "SpeedA", "Movement", $id, true);
        $this->lastMovements = [];
    }

    /**
     * @priority Highest
     */
    public function onPlayerMove(PlayerMoveEvent $ev): void {
        $player = $ev->getPlayer();
        $to = $ev->getTo();
        $from = $ev->getFrom();
        // Im still experimenting with values here
        $effLevel = ($player->getEffect(Effect::SPEED) instanceof EffectInstance) ? $player->getEffect(Effect::SPEED)->getEffectLevel() : 0;
        $allowed = ($player->getPing() * 0.008) + 0.7 + ($effLevel * 0.4);
        $distX = (($from->x - $to->x) ** 2);
        $distZ = (($from->z - $to->z) ** 2);
        $lastMovementTick = $this->lastMovements[$player->getId()] ?? $this->getServer()->getTick();
        // Get distance total (excluding y-axis)
        if (($this->getServer()->getTick() - $lastMovementTick) >= 2) {
            $this->debug('Movement check cancelled due to delayed last move on player: ' . $player->getName());
            $this->debug('DIFF:' . ($this->getServer()->getTick() - $lastMovementTick));
            $this->lastMovements[$player->getId()] = $this->getServer()->getTick();
            return;
            // To do: Add this in a lag handler
        }
        if (sqrt($distX + $distZ) > $allowed) {
            $this->increment($player->getName(), 1);
            $this->notifyAndIncrement($player, 2, 1, [
                "Distance" => sqrt($distX + $distZ),
                "Ping" => $player->getPing()
            ]);
            if ($this->isSuppressed()) {
                $ev->setCancelled(true);
            }
        }
        $this->lastMovements[$player->getId()] = $this->getServer()->getTick();
    }
}