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

namespace Bavfalcon9\Mavoric\Utils\Config;

use Closure;

class InvalidConfigError {
    public const KEY_MISSING = 0;
    public const VALUE_INVALID = 1;
    public const KEY_INVALID = 2;
    public const VALUE_EXCEEDED = 3;
    public const KEYS_NOT_EXACT = 4;
    public const VALUES_NOT_EXACT = 5;
    public const VALIDATION_FAILED = 6;

    /** @var int */
    protected $type;
    /** @var string */
    protected $detailedMessage;
    /** @var Closure|null */
    protected $requirement;
    /** @var array */
    protected $debugValues;

    public function __construct(int $type = 1, string $detailedMessage = null) {
        $this->type = $type;
        $this->detailedMessage = $detailedMessage ?? "Unknown error";
    }

    public function getType(): int {
        return $this->type;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setRequirement(Closure $closure): void {
        $this->requirement = $closure;
    }

    /**
     * @param $values array
     */
    public function setDebugValues($values): void {
        $this->debugValues = $values;
        return;
    }

    public function getDebugValues(): array {
        return $this->debugValues;
    }
}