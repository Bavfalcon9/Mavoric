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

namespace Bavfalcon9\Mavoric\Cheat;

use pocketmine\utils\TextFormat as TF;
use Bavfalcon9\Mavoric\Mavoric;
use Bavfalcon9\Mavoric\Loader;
use Bavfalcon9\Mavoric\Events\Module\ModuleDisabledEvent;

class CheatManager {
    /** @var string[] */
    public const MODULES = [
        'Movement',
        'Combat'
    ];
    /** @var Loader */
    private $plugin;
    /** @var Mavoric */
    private $mavoric;
    /** @var array */
    private $modules;
    /** @var bool */
    private $registered = false;

    /**
     * @param Loader $loader
     */
    public function __construct(Mavoric $mavoric, Loader $loader, Bool $autoRegister = false) {
        $this->mavoric = $mavoric;
        $this->plugin = $loader;

        if ($autoRegister) {
            $this->registerModules();
        }
    }

    /**
     * Registers all cheat modules
     * @return void
     * @prority 30
     */
    public function registerModules(): void {
        $modulesLoaded = 0;
        foreach (self::MODULES as $module) {
            // To do: Make a module class, new Module($name).
            $this->modules[$module] = [];
            $cheats = \scandir($this->getPathBase() . $module);
            foreach ($cheats as $cheat) {
                $cheat = explode('.php', $cheat)[0];
                if (in_array($cheat, ['.', '..'])) continue;
                if (class_exists($this->getClassBase() . $module . "\\" . $cheat)) {
                    $modulesLoaded++;
                    $class = '\\' . $this->getClassBase() . $module . '\\' . $cheat;
                    $detection = new $class($this->mavoric, $modulesLoaded);
                    if (!$detection->isEnabled()) {
                        $this->plugin->getLogger()->debug(TF::RED . "Cheat Detection: [$module] $cheat disabled with id $modulesLoaded");
                    } else {
                        $this->plugin->getServer()->getPluginManager()->registerEvents($detection, $this->plugin);
                        $this->plugin->getLogger()->debug(TF::GREEN . "Cheat Detection: [$module] $cheat enabled with id $modulesLoaded");
                    }
                }
            }
        }
    }

    /**
     * Unregister all cheat modules from mavoric.
     * @return void
     */
    public function disableModules(): void {
        foreach ($this->modules as $moduleName => $cheats) {
            $ev = new ModuleDisabledEvent($moduleName, $cheats);
            $ev->call();
            unset($this->modules[$moduleName]);
        }

        $this->modules = [];
    }

    /**
     * Register a cheat to the module name within the cheat provided
     * @param Cheat $cheat - Cheat to register
     * @return bool $registered
     */
    public function registerCheat(Cheat $cheat): bool {
        if (!isset($this->modules[$cheat->getModule()])) return false;
        
        // To Do: Change this when i finish module classes
        $module = &$this->modules[$cheat->getModule()];
        $exists = array_filter($module, function ($loadedCheat): bool {
            return ($loadedCheat->getName() === $cheat->getName());
        });

        if (count($exists) > 0) {
            return false;
        }

        $module[] = $cheat;
        return true;
    }

    /**
     * Gets the base path for the cheats.
     * @return string
     */
    protected function getPathBase(): string {
        return $this->plugin->getFilePath() . 'src/Bavfalcon9/Mavoric/Cheat/';
    }

    /**
     * Gets the base path for the cheats.
     * @return string
     */
    protected function getClassBase(): string {
        return "Bavfalcon9\\Mavoric\\Cheat\\";
    }
}