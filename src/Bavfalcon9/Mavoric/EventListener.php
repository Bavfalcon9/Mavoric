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
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use Bavfalcon9\Mavoric\Events\Player\PlayerVelocityEvent;
use Bavfalcon9\Mavoric\Events\Player\PlayerClickEvent;
use Bavfalcon9\Mavoric\Events\Violation\ViolationChangeEvent;
use Bavfalcon9\Mavoric\Tasks\KickTask;

class EventListener implements Listener {
    /** @var Mavoric */
    private $mavoric;
    /** @var Loader */
    private $plugin;
    /** @var Mixed[] */
    private $kbSession;

    public function __construct(Mavoric $mavoric, Loader $plugin) {
        $this->mavoric = $mavoric;
        $this->plugin = $plugin;
        $this->kbSession = [];
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onJoin(PlayerJoinEvent $ev): void {
        $player = $ev->getPlayer();
        $this->mavoric->getVerboseNotifier()->addIgnored($player->getName());
    }

    /**
     * Notifies staff about violation level (Non-verbose)
     */
    public function onViolationChange(ViolationChangeEvent $ev): void {
        if ($this->mavoric->tpsCheck->isHalted()) {
            return;
        }
        $violation = $ev->getViolation();

        if ($violation->getLastAdditionFromNow() >= 2 && $violation->getViolationCountSum() <= 4) {
            $this->mavoric->getViolationDataFor($ev->getPlayer()->getName())->clear();
            return;
        }

        $cNotifier = $this->mavoric->getCheckNotifier();
        $cNotifier->notify("§4[AC]§4: §c{$ev->getPlayer()->getName()} §7detected for §c{$ev->getCheat()}", "§8[§7{$violation->getCheatProbability()}§f% | §7VL §f{$ev->getCurrent()}§8]");

        if ($violation->getIncrementsInPastSecond() >= 20 && $ev->getPlayer()->getPing() <= 200) {
            $this->kickPlayer($ev->getPlayer(), '§4[Mavoric] Cheating [VC: ' . $violation->getViolationCountSum() . ']');
            $ev->getPlayer()->getServer()->broadcastMessage("§c{$ev->getPlayer()->getName()} §7has been autobanned for §c{$violation->getMostDetectedCheat()}");
            $cNotifier->notify("§4[AC]§4: §c{$ev->getPlayer()->getName()} §7has been banned for: " . $violation->getMostDetectedCheat(), "");
            $banList = $this->plugin->getServer()->getNameBans();
            $banList->addBan($ev->getPlayer()->getName(), '§4[Mavoric] Cheating [VC: ' . $violation->getViolationCountSum() . ']', new \DateTime("+7 Day"), 'Mavoric');
            return; 
        }
        if ($violation->getViolationCountSum() % 50 === 0 && $violation->getViolationCountSum() >= 50) {
            $cNotifier->notify("§4[AC]§4: §c{$ev->getPlayer()->getName()} §7is most likely cheating.", "");
            return;
        } 
        if ($violation->getViolationCountSum() % 80 === 0 && $violation->getViolationCountSum() >= 80) {
            $this->kickPlayer($ev->getPlayer(), '§4[Mavoric] Cheating [VC: ' . $violation->getViolationCountSum() . ']');
            $ev->getPlayer()->getServer()->broadcastMessage("§c{$ev->getPlayer()->getName()} §7has been autobanned for §c{$violation->getMostDetectedCheat()}");
            $cNotifier->notify("§4[AC]§4: §c{$ev->getPlayer()->getName()} §7has been banned for cheating.", "");
            $banList = $this->plugin->getServer()->getNameBans();
            $banList->addBan($ev->getPlayer()->getName(), '§4[Mavoric] Cheating [VC: ' . $violation->getViolationCountSum() . ']', new \DateTime("+7 Day"), 'Mavoric');
            return;
        }
    }

    /**
     * @param DataPacketReceiveEvent $ev
     */
    public function onClickCheck(DataPacketReceiveEvent $ev): void {
        if ($ev->getPacket() instanceof InventoryTransactionPacket) {
            if ($ev->getPacket()->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $event = new PlayerClickEvent($ev->getPlayer());
                $event->call();
            }
        } else if ($ev->getPacket() instanceof LevelSoundEventPacket) {
            if ($ev->getPacket()->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                $event = new PlayerClickEvent($ev->getPlayer());
                $event->call();
            }
        } 
    }

    /**
     * Kick a player from the server
     * @param Player $p - Player to kick
     * @param string $reason - Reason to kick
     */
    public function kickPlayer(Player $p, string $reason = 'Cheating'): void {
        $this->plugin->getScheduler()->scheduleDelayedTask(new KickTask($p, $reason), 1);
    }
}