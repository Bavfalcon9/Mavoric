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

namespace Bavfalcon9\Mavoric\Utils\Handlers\Pearl;

use pocketmine\scheduler\Task;
use Bavfalcon9\Mavoric\Utils\Handlers\PearlHandler;

class PearlPurgeTask extends Task {
    /** @var PearlHandler */
    private $handler;

    public function __construct(PearlHandler $handler) {
        $this->handler = $handler;
    }

    public function onRun(int $tick) {
        $this->handler->purge();
    }
}