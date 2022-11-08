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

use be\nnse\api\item\default\CustomItem;
use pocketmine\entity\Entity;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

abstract class FunctionalItem extends CustomItem implements Functional
{
    public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult
    {
        $itemUseResult = parent::onClickAir($player, $directionVector);
        $this->onUsing($player);
        return $itemUseResult;
    }

    public function onUsing(Player $player) : void
    {
    }

    public function onArmSwing(Player $player) : void
    {
    }

    public function onToggleSneaking(Player $player, bool $isSneaking) : void
    {
    }

    public function onJumping(Player $player) : void
    {
    }

    public function onAttacking(Player $player, Entity $victim) : void
    {
    }
}