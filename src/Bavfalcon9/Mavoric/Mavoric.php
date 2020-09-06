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

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginException;
use Bavfalcon9\Mavoric\Utils\Handlers\Pearl\FakePearl;
use Bavfalcon9\Mavoric\Utils\Notifier;
use Bavfalcon9\Mavoric\Utils\TpsCheck;
use Bavfalcon9\Mavoric\Utils\Handlers\BaseHandler;
use Bavfalcon9\Mavoric\Utils\Handlers\PearlHandler;
use Bavfalcon9\Mavoric\Cheat\CheatManager;
use Bavfalcon9\Mavoric\Cheat\Violation\ViolationData;

class Mavoric {
    /** @var string - Branch*/
    public static $MATH_MODE = 'master';
    /** @var TpsCheck */
    public $tpsCheck;
    /** @var Loader */
    private $plugin;
    /** @var Notifier */
    private $verboseNotifier;
    /** @var Notifier */
    private $checkNotifier;
    /** @var CheatManager */
    private $cheatManager;
    /** @var EventListener */
    private $eventListener;
    /** @var ViolationData[] */
    private $violations;
    /** @var BaseHandler[] */
    private $handlers;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
        $this->tpsCheck = new TpsCheck($plugin, $this);
        $this->verboseNotifier = new Notifier($this, $plugin);
        $this->checkNotifier = new Notifier($this, $plugin);
        $this->cheatManager = new CheatManager($this, $plugin, true);
        $this->eventListener = new EventListener($this, $plugin);
        $this->violations = [];
        $this->handlers = [];

        /** Registeration */
        $this->handlers[] = new Pearlhandler($plugin);
        ItemFactory::registerItem(new FakePearl(), true);

        /** Other checks */
        if (!class_exists('pocketmine\math\Facing')) {
            // Deprecated support
            //throw new PluginException('pocketmine\math dependency out of date. Mavoric requires branch "master" or later. Update it with composer.');
            //$plugin->getLogger()->critical('Using mathlib 0.2 instead of master (this will be deprecated soon)');
            $plugin->getLogger()->critical('pocketmine\math dependency out of date. Mavoric will require branch "master" or later upon next release. Update it with composer.');
            self::$MATH_MODE = '0.2';
        }
    }

    /**
     * Unloads all modules and commands.
     * @return void
     */
    public function disable(): void {
        $this->cheatManager->disableModules();
    }

    /**
     * Gets the violation level data for a player
     */
    public function getViolationDataFor(string $player): ?ViolationData {
        if (!isset($this->violations[$player])) {
            $resolvedPlayer = $this->plugin->getServer()->getPlayer($player);
            if ($resolvedPlayer) {
                $this->violations[$player] = new ViolationData($resolvedPlayer);
            } else {
                return null;
            }
        }

        return $this->violations[$player]->forceUpdateStoredPlayer();
    }

    /**
     * Gets the check notifier
     * @return Notifier
     */
    public function getCheckNotifier(): Notifier {
        return $this->checkNotifier;
    }

    /**
     * Gets the verbose notifier
     * @return Notifier
     */
    public function getVerboseNotifier(): Notifier {
        return $this->verboseNotifier;
    }
}