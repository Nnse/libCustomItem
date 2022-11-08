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

namespace be\nnse\api\item;

use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\SingletonTrait;

class CustomItemFactory
{
    use SingletonTrait;

    /** @var ICustomItem[] */
    private array $customItems = [];

    /**
     * @param ICustomItem $customItem
     * @param bool $addCreativeInventory
     * @return void
     */
    public function register(ICustomItem $customItem, bool $addCreativeInventory = false) : void
    {
        /** @var Item|ICustomItem $customItem */
        $this->customItems[$customItem::class] = $customItem;
        ItemFactory::getInstance()->register($customItem, true);

        if ($addCreativeInventory) {
            CreativeInventory::getInstance()->add($customItem);
        }
    }

    /**
     * @param string $className
     * @return Item|null
     */
    public function get(string $className) : ?Item
    {
        $customItem = $this->customItems[$className];
        if ($customItem instanceof Item) {
            return $customItem;
        }
        return null;
    }
}