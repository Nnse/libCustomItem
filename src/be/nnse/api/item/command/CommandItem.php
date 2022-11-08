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

namespace be\nnse\api\item\command;

use be\nnse\api\item\default\CustomItem;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

abstract class CommandItem extends CustomItem
{
    const TAG_COMMAND = "command";

    public function __construct(
        int $id,
        int $meta,
        string $command,
        string $identifyName = "Unknown",
        string $customName = "",
        array $lore = []
    )
    {
        parent::__construct($id, $meta, $identifyName, $customName, $lore);
        $this->getNamedTag()->setString(self::TAG_COMMAND, $command);
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
    public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult
    {
        $itemUseResult = parent::onClickAir($player, $directionVector);
        $this->executeCommand($player);
        return $itemUseResult;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function executeCommand(Player $player) : void
    {
        $command = $this->getNamedTag()->getString(self::TAG_COMMAND, "");
        if ($command !== "") {
            $player->chat("/" . $command);
        }
    }
}