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

namespace Bavfalcon9\Mavoric\Utils;

use Bavfalcon9\Mavoric\Loader;
use Bavfalcon9\Mavoric\Mavoric;

class Notifier {
    /** @var string[] */
    private $ignored;
    /** @var float[] */
    private $times;
    /** @var Loader */
    private $plugin;
    /** @var Mavoric */
    private $mavoric;

    public function __construct(Mavoric $mavoric, Loader $plugin) {
        $this->times = [];
        $this->ignored = [];
        $this->plugin = $plugin;
        $this->mavoric = $mavoric;
    }

    /**
     * Notify online members with proper permission
     * @param string $message - Message to notify as
     * @param string $append - Message to append
     */
    public function notify(string $message, string $append = ''): void {
        if (isset($this->times[$message]) && $this->times[$message] + 2 >= microtime(true)) {
            return;
        }

        $onlinePlayers = $this->plugin->getServer()->getOnlinePlayers();

        foreach ($onlinePlayers as $player) {
            if ($player->hasPermission('mavoric.alerts')) {
                if (in_array($player->getName(), $this->ignored)) continue;
                $player->sendMessage($message . " " . $append);
            }
        }
        
        $this->times[$message] = microtime(true);

        foreach ($this->times as $msg=>$time) {
            if ($time + 2 <= microtime(true)) {
                unset($this->times[$msg]);
            }
        }
        return;
    }

    /**
     * @param string $player - Username
     * @return bool
     */
    public function addIgnored(string $player): bool {
        if (in_array($player, $this->ignored)) {
            return false;
        } else {
            $this->ignored[] = $player;
            return true;
        }
    }

    /**
     * @param string $player - Username
     * @return bool
     */
    public function removeIgnored(string $player): bool {
        if (!in_array($player, $this->ignored)) {
            return false;
        } else {
            array_splice($this->ignored, array_search($player, $this->ignored));
            return true;
        }
    }
}