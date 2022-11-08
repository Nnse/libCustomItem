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

namespace be\nnse\api\item\default;

use be\nnse\api\item\ICustomItem;
use pocketmine\item\Durable;
use pocketmine\item\ItemIdentifier;
use pocketmine\utils\TextFormat;

abstract class CustomDurable extends Durable implements ICustomItem
{
    /**
     * @param int $id
     * @param int $meta
     * @param string $customName
     * @param array $lore
     */
    public function __construct(
        int $id,
        int $meta,
        string $customName = "",
        array $lore = []
    )
    {
        parent::__construct(new ItemIdentifier($id, $meta), TextFormat::clean($customName));

        if ($customName !== "") {
            if ($customName === TextFormat::clean($customName)) {
                $this->setCustomName(TextFormat::RESET . $customName);
            } else {
                $this->setCustomName($customName);
            }
        }
        if (!empty($lore)) $this->setLore($lore);
    }
}