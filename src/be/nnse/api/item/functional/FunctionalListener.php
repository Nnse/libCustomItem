<?php

/*
 *
 *  _ __  _ __  ___  ___
 * | '_ \| '_ \/ __|/ _ \
 * | | | | | | \__ \  __/
 * |_| |_|_| |_|___/\___|
 *
 * This program is free software: you can redistribute it and/or modify
 * It under the terms of the MIT License.
 *
 * @author nnse
 * @link   https://github.com/nnse
 * @license https://opensource.org/licenses/MIT
 *
 *
 */

declare(strict_types=1);

namespace be\nnse\api\item\functional;

use be\nnse\api\item\utils\PlayerTemporaryDataTrait;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class FunctionalListener implements Listener
{
    /**
     * @param PluginBase $pluginBase
     * @return void
     */
    public static function register(PluginBase $pluginBase) : void
    {
        Server::getInstance()->getPluginManager()->registerEvents(new self(), $pluginBase);
    }

    /**
     * @priority LOWEST
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event) : void
    {
        $player = $event->getOrigin()->getPlayer();

        $pk = $event->getPacket();
        if ($pk instanceof LevelSoundEventPacket) {
            if ($pk->sound === LevelSoundEvent::ATTACK_NODAMAGE) {
                $item = $player->getInventory()->getItemInHand();
                if ($item instanceof Functional) {
                    $item->onArmSwing($player);
                }
            }
        }
    }

    /**
     * @priority LOWEST
     */
    public function onToggleSneak(PlayerToggleSneakEvent $event) : void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item instanceof Functional) {
            $item->onToggleSneaking($player, $event->isSneaking());
        }
    }

    /**
     * @priority LOWEST
     */
    public function onJump(PlayerJumpEvent $event) : void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($item instanceof Functional) {
            $item->onJumping($player);
        }
    }

    /**
     * @priority LOWEST
     */
    public function onDamage(EntityDamageByEntityEvent $event) : void
    {
        $damager = $event->getDamager();
        $victim = $event->getEntity();
        if (!($damager instanceof Player)) return;

        $item = $damager->getInventory()->getItemInHand();
        if ($item instanceof Functional) {
            $item->onAttacking($damager, $victim);
        }
    }

    /**
     * @priority MONITOR
     */
    public function onQuit(PlayerQuitEvent $event) : void
    {
        $player = $event->getPlayer();
        $deleteTempData = function (array $items) use ($player) {
            foreach ($items as $item) {
                if ($item instanceof Functional) {
                    /** @var PlayerTemporaryDataTrait $item */
                    $item->deletePlayerTemporaryData($player);
                }
            }
        };

        $deleteTempData($player->getInventory()->getContents());
        $deleteTempData($player->getArmorInventory()->getContents());
        $deleteTempData($player->getOffHandInventory()->getContents());
        $deleteTempData($player->getCursorInventory()->getContents());
        $deleteTempData($player->getEnderInventory()->getContents());
    }
}