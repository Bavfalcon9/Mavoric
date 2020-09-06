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
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class SpeedB extends Cheat {

    private $lastDistance = [];
    private $lastTime = [];

    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "SpeedB", "Movement", $id, false);
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();

        $from = $event->getFrom();
        $to = $event->getTo();
        
        $dX = $to->x - $from->x;
        $dZ = $to->z - $from->z;

        if(!isset($this->lastDistance[$player->getName()])) {
            $this->lastDistance[$player->getName()] = [
                0 => $dX,
                1 => $dZ
            ];
            return;
        }
        
        if(!isset($this->lastTime[$player->getName()])) $this->lastTime[$player->getName()] = microtime(true);

        $expected = 0.82567;
        if($player->getEffect(1) !== null){
            if($player->getEffect(1)->getEffectLevel() != 0){
                $expected = 0.82567 + ($player->getEffect(1)->getEffectLevel() * 0.2) * 0.82567;
            }
        }

        // if the last moved tick was 2 ticks ago
        $currentTime = microtime(true);
        $time = $currentTime - $this->lastTime[$player->getName()];
        $ticks = round($time * 20, 2);
        if($ticks >= 2){
            if($player->getEffect(1) !== null){
                if($player->getEffect(1)->getEffectLevel() != 0){
                    $expected = 0.82567 + ($player->getEffect(1)->getEffectLevel() * 0.2) * 0.82567;
                }
            } else {
                $expected = 0.82567 * $ticks;
            }
        }

        $speeds = [$dX, $dZ];
        $this->debug("Expected speed is: " . $expected . "\n");
        $this->debug("Speed Given: [DX: " . $dX . " | DZ: " . $dZ . "]\n");
        

        if($dX > $expected || $dX < -$expected) {
            if($this->lastDistance[$player->getName()][0] >= $expected){
                $this->increment($player->getName(), 1);
                $this->notifyAndIncrement($player, 2, 1, [
                    "TPS" => $player->getServer()->getTicksPerSecond(),
                    "Ping" => $player->getPing()
                ]);
            }
        }

        if($dZ > $expected || $dZ < -$expected) {
            if($this->lastDistance[$player->getName()][1] >= $expected){
                $this->increment($player->getName(), 1);
                $this->notifyAndIncrement($player, 2, 1, [
                    "TPS" => $player->getServer()->getTicksPerSecond(),
                    "Ping" => $player->getPing()
                ]);
            }
        }

        $this->lastDistance[$player->getName()] = [
            0 => $dX,
            1 => $dZ
        ];

        unset($this->lastTime[$player->getName()]);
        $this->lastTime[$player->getName()] = microtime(true);
    }

}