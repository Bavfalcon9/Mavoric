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

class AutoclickerB extends Cheat{

    private $previousClick = [];
    private $allClicks = [];

    private $allDeviations = [];
    private $averageDeviations = [];

    private $level = [];

    public function __construct(Mavoric $mavoric, int $id = -1) {
        parent::__construct($mavoric, "AutoclickerB", "Combat", $id, false);
    }

    public function onClick(PlayerClickEvent $ev): void {
        $player = $ev->getPlayer();
        $name = $player->getName();
        if(!isset($this->previousClick[$name])){
            $this->previousClick[$name] = microtime(true) * 1000;
            $this->allClicks[$name] = [];
            $this->allDeviations[$name] = [];
            $this->averageDeviations[$name] = [];
            $this->level[$name] = 0;
            return;
        }
        $currentTime = microtime(true) * 1000;
        $time = $currentTime - $this->previousClick[$name];
        if($time > 1000){
            $this->previousClick[$name] = microtime(true) * 1000;
            return;
        }
        array_push($this->allClicks[$name], $time);
        $this->previousClick[$name] = microtime(true) * 1000;
        if(count($this->allClicks[$name]) < 5) return;
        $averageTime = array_sum($this->allClicks[$name]) / count($this->allClicks[$name]);
        $deviation = abs($time - $averageTime);
        array_push($this->allDeviations[$name], $deviation);
        if(count($this->allDeviations[$name]) < 5) return;
        $averageDeviation = array_sum($this->allDeviations[$name]) / count($this->allDeviations[$name]);
        //$this->getServer()->broadcastMessage("Average Deviation: $averageDeviation");
        array_push($this->averageDeviations[$name], $averageDeviation);
        if(count($this->averageDeviations[$name]) < 10) return;
        if($averageDeviation < 20 && count($this->allDeviations[$name]) >= 20){
            $badDeviations = [];
            foreach($this->allDeviations[$name] as $number){
                if($number < 11) array_push($badDeviations, $number);
            }
            $badCount = count($badDeviations);
            if($badCount >= 25){
                $this->level[$name] = $this->level[$name] + 1;
                if($this->level[$name] >= 2.5){
                    $this->increment($player->getName(), 1);
                    $this->notifyAndIncrement($player, 4, 1, [
                        "Ping" => $player->getPing()
                    ]);
                    $this->level[$name] = 1;
                }
            } else {
                $minDeviation = min($this->averageDeviations[$name]);
                $maxDeviation = max($this->averageDeviations[$name]);
                $diffrence = $maxDeviation - $minDeviation;
                if($diffrence <= 2.5){
                    $this->increment($player->getName(), 1);
                    $this->notifyAndIncrement($player, 4, 1, [
                        "Ping" => $player->getPing()
                    ]);
                    $this->level[$name] = 1;
                } else {
                    $this->level[$name] = $this->level[$name] * 0.5;
                }
            }
            $badDeviations = [];
        }
        if(count($this->allClicks[$name]) >= 45){
            unset($this->allClicks[$name]);
            $this->allClicks[$name] = [];
        }
        if(count($this->allDeviations[$name]) >= 55){
            unset($this->allDeviations[$name]);
            $this->allDeviations[$name] = [];
        }
        if(count($this->averageDeviations[$name]) >= 55){
            unset($this->averageDeviations[$name]);
            $this->averageDeviations[$name] = [];
        }
    }

}