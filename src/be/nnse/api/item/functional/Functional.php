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

use pocketmine\entity\Entity;
use pocketmine\player\Player;

interface Functional
{
    /**
     * Trigger when player use this item.
     * @param Player $player
     * @return void
     */
    public function onUsing(Player $player) : void;

    /**
     * Trigger when player swings arm with item.
     * @param Player $player
     * @return void
     */
    public function onArmSwing(Player $player) : void;

    /**
     * Trigger when player sneaks with item.
     * @param Player $player
     * @param bool $isSneaking
     * @return void
     */
    public function onToggleSneaking(Player $player, bool $isSneaking) : void;

    /**
     * Trigger when player jumps with item.
     * @param Player $player
     * @return void
     */
    public function onJumping(Player $player) : void;

    /**
     * Trigger when player attacks with item.
     * @param Player $player
     * @param Entity $victim
     * @return void
     */
    public function onAttacking(Player $player, Entity $victim) : void;
}