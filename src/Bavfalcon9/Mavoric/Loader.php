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

namespace Bavfalcon9\Mavoric;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Living;
use Bavfalcon9\Mavoric\Command\AlertCommand;
use Bavfalcon9\Mavoric\Command\LogCommand;

class Loader extends PluginBase {
    /** @var Mavoric */
    private $mavoric;

    /**
     * Enable mavoric
     * @return void
     */
    public function onEnable(): void {
        //if ($this->getServer()->getConfigBool('mavoric_dev') === true) { 
            $this->mavoric = new Mavoric($this);
            $commandMap = $this->getServer()->getCommandMap();
            $commandMap->registerAll('Mavoric', [
                new AlertCommand($this, $this->mavoric),
                new LogCommand($this, $this->mavoric)
            ]);
        //} else {
        //    $this->getLogger()->critical('Mavoric Development builds are not allowed on this server.');
        //    $this->getServer()->getPluginManager()->disablePlugin($this);
        //}
    }

    /**
     * Disable mavoric properly
     * @return void
     */
    public function onDisable(): void {
        if ($this->mavoric === null) return;
        $this->mavoric->disable();
    }

    /**
     * @return Mavoric|null
     */
    public function getMavoric(): ?Mavoric {
        return $this->mavoric;
    }

    /**
     * @return string
     */
    public function getFilePath(): string {
        return $this->getFile();
    }
}