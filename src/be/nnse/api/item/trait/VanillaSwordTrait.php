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

trait VanillaSwordTrait
{
    public function getBlockToolType() : int
    {
        return BlockToolType::SWORD;
    }

    public function getAttackPoints() : int
    {
        /** @var TieredTool $this */
        return $this->tier->getBaseAttackPoints();
    }

    public function getBlockToolHarvestLevel() : int
    {
        return 1;
    }

    public function getMiningEfficiency(bool $isCorrectTool) : float
    {
        /** @var TieredTool $this */
        return $this->getMiningEfficiency($isCorrectTool) * 1.5; //swords break any block 1.5x faster than hand
    }

    protected function getBaseMiningEfficiency() : float
    {
        return 10;
    }

    public function onDestroyBlock(Block $block) : bool
    {
        /** @var TieredTool $this */
        if (!$block->getBreakInfo()->breaksInstantly()) {
            return $this->applyDamage(2);
        }
        return false;
    }

    public function onAttackEntity(Entity $victim) : bool
    {
        /** @var TieredTool $this */
        return $this->applyDamage(1);
    }
}