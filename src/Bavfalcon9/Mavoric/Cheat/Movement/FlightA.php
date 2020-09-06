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
use pocketmine\block\Air;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Effect;
use pocketmine\math\Vector3;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;
use Bavfalcon9\Mavoric\Utils\EffectUtils;

class FlightA extends Cheat {
    /** @var float[] */
    private $damageTimes;
    /** @var array[] */
    private $locationSessions;

    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "FlightA", "Movement", $id, true);
        $this->damageTimes = [];
        $this->locationSessions = [];
    }

    public function onDamaged(EntityDamageByEntityEvent $ev): void {
        if ($ev->getEntity() instanceof Player) {
            $this->damageTimes[$ev->getEntity()->getName()] = microtime(true);
        }
    }

    public function onPlayerMove(PlayerMoveEvent $ev): void {
        $player = $ev->getPlayer();
        $from = $ev->getFrom();
        $to = $ev->getTo();

        if (!isset($this->locationSessions[$player->getName()])) $this->locationSessions[$player->getName()] = [];
        array_push($this->locationSessions[$player->getName()], [microtime(true), $from]);

        if (sizeof($this->locationSessions[$player->getName()]) >= 5) array_shift($this->locationSessions[$player->getName()]);
        $session = $this->locationSessions[$player->getName()];

        if ($player->getAllowFlight() === true) return;
        if ($player->isImmobile()) return;
        if ($player->getArmorInventory()->getChestPlate()->getId() === 444) return;
        // To do: Implement proper calculations for effects (IMPLEMENT THIS CLASS)
        //if (EffectUtils::getEffectLevel($player, Effect::LEVITATION) !== 0 || EffectUtils::getEffectLevel($player, Effect::JUMP_BOOST) !== 0) {
        //    $player->resetFallDistance();
        //}

        if (Mavoric::$MATH_MODE === '0.2') {
            $blockAbove = $player->getLevel()->getBlock($player)->getSide(Vector3::SIDE_UP);
            $blockUnder = $blockAbove->getSide(Vector3::SIDE_DOWN);
        } else {
            $blockAbove = $player->getLevel()->getBlock($player)->getSide(\pocketmine\math\Facing::UP);
            $blockUnder = $blockAbove->getSide(\pocketmine\math\Facing::DOWN);        
        }

        if ($session[0][0] + 2 <= microtime(true)) {
            $this->locationSessions[$player->getName()] = [];
            $distAvg = 0;
        } else {
            $sessionXZ = $session[0][1];
            $sessionXZ2 = isset($session[count($session) - 1]) ? $session[count($session) - 1][1] : $to;
            $sessionXZ->y = 0;
            $sessionXZ2->y = 0;
            $distAvg = $sessionXZ->distance($sessionXZ2);
        }

        $airTime = $player->getInAirTicks() - ($player->getPing() / 20); // divide by 20 to convert to ticks
        $yDistance = $to->y - $from->y;
        $xyzDistance = $from->distance($to);
        
        $lastDamageTime = microtime(true) - ($this->damageTimes[$player->getName()] ?? 0);

        if ($lastDamageTime <= 3) return;
        
        if ($blockUnder instanceof Air) {
            if ($distAvg >= 2 && $airTime >= 10) {
                // checks XZ flight, over a short period, pesky hackers...
                $this->increment($player->getName(), 1);
                $this->notifyAndIncrement($player, 4, 1, [
                    "AirTime" => $airTime,
                    "DistAvg" => $distAvg,
                    "Ping" => $player->getPing()
                ]);
                return;
            }

            if ($yDistance >= 0 && $airTime >= 100) {
                // we need to check for jump boost effect here
                $this->increment($player->getName(), 1);
                $this->notifyAndIncrement($player, 4, 1, [
                    "AirTime" => $airTime,
                    "DistY" => $yDistance,
                    "Ping" => $player->getPing()
                ]);
                return;
            }

            if ($airTime >= 500) {
                $this->debug('Expected air cheat');
                $this->increment($player->getName(), 1);
                $this->notifyAndIncrement($player, 4, 1, [
                    "AirTime" => $airTime,
                    "Ping" => $player->getPing()
                ]);
                return;
            }
        }
    }
}