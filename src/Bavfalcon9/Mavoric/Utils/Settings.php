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

use pocketmine\utils\Config;
use Bavfalcon9\Mavoric\Utils\Config\ConfigValidatorTrait;
use Bavfalcon9\Mavoric\Utils\Config\InvalidConfigError;

/**
 * Instaniated by Mavoric with config settings
 */
class Settings {
    use ConfigValidatorTrait;

    /** @var Config */
    protected $config;
    
    public function __construct(Config $config) {
        $this->config = $config;
        $this->addValidator('Version', function ($val): bool { return is_string($val); });
        $this->addValidator('Webhooks', function ($val): bool {
            if (is_array($val)) {
                foreach ($val as $nestedVal) {
                    if (!(is_string($nestedVal) || $nestedVal === false)) return false;
                }
                return true;
            } else {
                return false;
            }
        });
        $this->addValidator('Options', function ($val): bool {
            if (is_array($val)) {
                if (array_keys($val) === ['developer-mode', 'packet-detection', 'default-violation-limit']) {
                    foreach (array_values($val) as $nestedVal) {
                        if (!(is_bool($nestedVal) || is_numeric($nestedVal))) return false;
                    }
                }
                return true;
            } else {
                return false;
            }
        });
        $this->addValidator('Modules', function ($modules): bool {
            if (!is_array($modules)) return false;
            foreach ($modules as $module) {
                if (!is_array($module)) return false;
                if (array_keys($module) !== ['enabled', 'Checks']) return false;
                if (!is_array($module['Checks'])) return false;
                foreach ($module['Checks'] as $cheatName => $cheatSetting) {
                    // To do
                }
            }
            return true;
        });
    }
}