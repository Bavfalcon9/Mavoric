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
use pocketmine\block\Transparent;
use pocketmine\block\Fallable;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\math\Facing;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Cheat\CheatManager;
use Bavfalcon9\Mavoric\Utils\Handlers\PearlHandler;

class NoClip extends Cheat {
    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "NoClip", "Movement", $id, true);
    }

    public function onPlayerMove(PlayerMoveEvent $ev): void {
        $player = $ev->getPlayer();
        $AABB = $player->getBoundingBox();
        $blockA = $player->getLevel()->getBlock($player);
        $blockB = $player->getLevel()->getBlock($player->floor()->up(1));
        $throw = PearlHandler::recentThrowFromWithin($player->getName(), 4);
        $pos = (!$throw) ? null : $throw->getLandingLocation();

        if ($player->getAllowFlight()) return;
        if ($blockA->collidesWithBB($AABB) || $blockB->collidesWithBB($AABB)) {
            if ($pos !== null) {
                # Bad pearl
                $player->teleport($throw->getThrowLocation());
                return;
            }

            foreach ([$blockA, $blockB] as $block) {
                // To do, better AABB checks.
                if ($block instanceof Transparent || $block instanceof Fallable) {
                    $this->debug('Transparent/Fallable check triggered, so, avoided for: ' . $player->getName());
                    return;
                }
                foreach ($block->getCollisionBoxes() as $bb) {
                    if (!$bb->isVectorInside($player)) {
                        $this->debug('Positive noclip check, Vector remains inside bounding box of: ' . $player->getName());
                        $this->increment($player->getName(), 1);
                        $this->notifyAndIncrement($player, 2, 1, [
                            "BlockA" => $blockA->getName(),
                            "BlockB" => $blockB->getName(),
                            "Ping" => $player->getPing()
                        ]);

                        if ($this->isSuppressed()) {
                            $ev->setCancelled(true);
                        }
                        return;
                    }
                }
            }
        }
    }
}