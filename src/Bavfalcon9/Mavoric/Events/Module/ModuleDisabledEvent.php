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

namespace Bavfalcon9\Mavoric\Events\Module;

use pocketmine\event\Event;
use Bavfalcon9\Mavoric\Mavoric;

/**
 * Called when mavoric modules are disabled, this is not cancellable.
 */
class ModuleDisabledEvent extends Event {
    /** @var string */
    protected $module;
    /** @var string[] */
    protected $cheats;

    public function __construct(string $module, array $cheats = []) {
        $this->module = $module;
        $this->cheats = $cheats;
    }

    /**
     * Returns the module name
     * @return string
     */
    public function getModule(): string {
        return $this->module;
    }

    /**
     * Returns the cheats consisted in the module
     * @return string[]
     */
    public function getCheats(): array {
        return $this->cheats;
    }
}