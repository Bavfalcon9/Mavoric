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

namespace Bavfalcon9\Mavoric\Cheat\Combat;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;

class MultiAura extends Cheat {
    /** @var array */
    private $attacks;

    public function __construct(Mavoric $mavoric, int $id = -2) {
        parent::__construct($mavoric, "MultiAura", "Combat", $id, true);
        $this->attacks = [];
    }

    /**
     * @param EntityDamageByEntityEvent $ev
     * @return void
     */
    public function onAttack(EntityDamageByEntityEvent $ev): void {
        $damager = $ev->getDamager();
        $damaged = $ev->getEntity();

        if (!($damager instanceof Player)) return;
        if ($damaged->getLastDamageCause() instanceof EntityDamageByChildEntityEvent) return;
        if (isset($this->attacks[$damager->getName()]) && $this->attacks[$damager->getName()]["time"] + 0.25 <= microtime(true)) {
            unset($this->attacks[$damager->getName()]);
            return;
        }

        if (!isset($this->attacks[$damager->getName()])) {
            $this->attacks[$damager->getName()] = [
                "time" => microtime(true),
                "hits" => []
            ];
        }

        array_push($this->attacks[$damager->getName()]["hits"], $damaged);
        $attack = &$this->attacks[$damager->getName()];
        $unique = array_unique($attack["hits"]);
        
        if (count($unique) >= 4) {
            $this->increment($damager->getName(), 1);
            $this->notifyAndIncrement($damager, 2, 1, [
                "Entity" => $damaged->getId(),
                "UniqueHits" => count($unique),
                "Ping" => $damager->getPing()
            ]);
            return;
        }

        if (count($attack["hits"]) >= 6) {
            $lastEntity = $damager;
            foreach ($attack["hits"] as $entity) {
                if ($lastEntity === $damager) {
                    continue;
                }
                if ($entity->distance($lastEntity) >= 2) {
                    $this->increment($damager->getName(), 1);
                    $this->notifyAndIncrement($damager, 4, 1, [
                        "Entity" => $damaged->getId(),
                        "Hits" => count($attack['hits']),
                        "Ping" => $damager->getPing()
                    ]);
                    return;
                }

                $lastEntity = $entity;
            }
        }
    }
}