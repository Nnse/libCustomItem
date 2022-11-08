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

namespace be\nnse\api\item\trait;

use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\entity\Entity;
use pocketmine\item\TieredTool;

trait VanillaAxeTrait
{
    public function getBlockToolType() : int
    {
        return BlockToolType::AXE;
    }

    public function getBlockToolHarvestLevel() : int
    {
        /** @var TieredTool $this */
        return $this->tier->getHarvestLevel();
    }

    public function getAttackPoints() : int
    {
        /** @var TieredTool $this */
        return $this->tier->getBaseAttackPoints() - 1;
    }

    public function onDestroyBlock(Block $block) : bool
    {
        /** @var TieredTool $this */
        if (!$block->getBreakInfo()->breaksInstantly()) {
            return $this->applyDamage(1);
        }
        return false;
    }

    public function onAttackEntity(Entity $victim) : bool
    {
        /** @var TieredTool $this */
        return $this->applyDamage(2);
    }
}