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

namespace Bavfalcon9\Mavoric\Utils\Handlers;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDespawnEvent;
use Bavfalcon9\Mavoric\Loader;
use Bavfalcon9\Mavoric\Utils\Handlers\Pearl\PearlThrownEvent;
use Bavfalcon9\Mavoric\Utils\Handlers\Pearl\PearlThrow;
use Bavfalcon9\Mavoric\Utils\Handlers\Pearl\PearlPurgeTask;

class PearlHandler extends BaseHandler {
    /** @var PearlThrow[] */
    private static $throws = [];
    /** @var PearlHandler */
    private static $instance;
    /** @var Loader */
    private $plugin;

    public function __construct(Loader $plugin) {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
        $plugin->getScheduler()->scheduleRepeatingTask(new PearlPurgeTask($this), 20 * 10);
        $this->plugin = $plugin;
        self::$throws = [];
        self::$instance = $this;
    }

    /**
     * Triggered when a enderpearl is thrown
     * @param PearlThrownEvent $event - PearlThrownEvent
     * @return void
     */
    public function onThrow(PearlThrownEvent $event): void {
        $player = $event->getPlayer();
        $entity = $event->getEntity();

        if ($event->isCancelled()) {
            return;
        }

        $throw = new PearlThrow($player, $entity->getId());
        self::$throws[] = $throw;;
        return;
    }

    /**
     * Triggered when a entity is despawned
     * @param EntityDespawnEvent $event - EntityDespawnEvent
     * @return void
     */
    public function onDespawn(EntityDespawnEvent $event): void {
        if ($event->isProjectile()) {
            $id = $event->getEntity()->getId();

            foreach (self::$throws as &$throw) {
                if ($throw->getPearlEntityId() === $id) {
                    $throw->setCompleted(microtime(true), $event->getEntity()->getPosition());
                }
            }
        }
    }

    /**
     * Clears throws from memory for performance reasons.
     * @return int
     */
    public function purge(): int {
        $purged = 0;
        foreach (self::$throws as $index=>$throw) {
            if ($throw->getLandingTime() + 10 <= time()) {
                unset(self::$throws[$index]);
                $purged++;
            }
        }
        return $purged;
    }

    /**
     * Returns a list of throws from a player
     * @param string $player - Player name
     * @return PearlThrow[] - Array of throws
     */
    public static function getThrowsFrom(string $player): Array {
        $allThrows = [];

        foreach (self::$throws as $throw) {
            if ($throw->getPlayer()->getName() === $player) {
                $allThrows[] = $throw;
            }
        }

        return $allThrows;
    }

    /**
     * Returns the most recent throw from a player
     * @param string $player - Player name
     * @return PearlThrow|null - The most recent thrown pearl from player
     */
    public static function getMostRecentThrowFrom(string $player): ?PearlThrow {
        $throws = self::getThrowsFrom($player);
        $recent = null;

        foreach ($throws as $throw) {
            if (!$recent) {
                $recent = $throw;
                continue;
            }

            if ($recent->getThrownTime() > $throw->getThrownTime()) {
                $recent = $throw;
                continue;
            }
        }

        return $recent;
    }

    /**
     * Gets the most recent throw from a player within the provided time of the pearl landing
     * @param string $player - Player name
     * @param int $time - Time span
     */
    public static function recentThrowFromWithin(string $player, int $time = 2): ?PearlThrow {
        $throw = self::getMostRecentThrowFrom($player);
        if ($throw !== null) {
            if ($throw->getLandingTime() + $time >= time()) {
                return $throw;
            }
        }
        return null;
    }

    /**
     * Gets the pearlHandler instance.
     */
    public static function getInstance(): ?PearlHandler {
        return self::$instance;
    }
}