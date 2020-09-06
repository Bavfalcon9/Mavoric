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

namespace Bavfalcon9\Mavoric\Tasks;

use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Loader;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class TpsCheckTask extends AsyncTask {
    private $lastTick;
    private $callback;

    public function __construct($lastTick, $callback) {
        $this->lastTick = $lastTick;
        $this->callback = $callback;
    }

    public function onRun() {
        sleep(1);
    }

    public function onCompletion(Server $server) {
        $expected = $this->lastTick + 20;
        $actual = $server->getTick();
        $diff = $expected - $actual;
        $callback = $this->callback;
        $callback($server, $diff);
    }
}