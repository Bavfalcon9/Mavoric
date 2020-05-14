<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| "__| |/ __|
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
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Cheat\Cheat;
use Bavfalcon9\Mavoric\Events\Player\PlayerClickEvent;

class Autoclicker extends Cheat {
    /** @var int[] */
    private $cps;
    private $constant;

    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "Autoclicker", "Combat", $id, true);
        $this->cps = [];
        $this->constant = [];
    }

    /**
     * @param PlayerClickEvent $ev
     */
    public function onClick(PlayerClickEvent $ev) {
        $this->dueCheck($ev->getPlayer());
    }

    private function dueCheck(Player $player): void {
        if (!isset($this->cps[$player->getName()])) {
            $this->cps[$player->getName()] = [];
        }

        $time = microtime(true);

        array_push($this->cps[$player->getName()], microtime(true));

        $cps = count(array_filter($this->cps[$player->getName()],  function (float $t) use ($time) : bool {
            return ($time - $t) <= 1;
        }));
        
        if ($cps >= 100) {
            $this->increment($player->getName(), 1);
            $this->notifyAndIncrement($player, 2, 100, [
                "CPS" => $cps,
                "Ping" => $player->getPing()
            ]);
            return;
        }

        if ($cps >= 22) {
            $this->increment($player->getName(), 1);
            $this->notifyAndIncrement($player, 2, 1, [
                "CPS" => $cps,
                "Ping" => $player->getPing()
            ]);
        }
       
    }
    
}
