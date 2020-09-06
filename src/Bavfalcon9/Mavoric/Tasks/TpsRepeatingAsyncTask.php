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

use Bavfalcon9\Mavoric\Utils\TpsCheck;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class TpsRepeatingAsyncTask extends Task {
    private $tps;
    public function __construct(TpsCheck $tps) {
        $this->tps = $tps;
    }

    public function onRun(int $tick) {
        $this->tps->runAsyncTask();
    }
}