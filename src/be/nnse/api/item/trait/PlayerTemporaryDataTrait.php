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

use pocketmine\player\Player;

trait PlayerTemporaryDataTrait
{
    /** @var array|array[] */
    private static array $playerTemporaryData = [[]];

    /**
     * @param Player $player
     * @param string $childKey
     * @return bool
     */
    public static function hasPlayerTemporaryData(Player $player, string $childKey) : bool
    {
        return self::getPlayerTemporaryData($player, $childKey) !== null;
    }

    /**
     * @param Player $player
     * @param string $childKey
     * @return mixed
     */
    public static function getPlayerTemporaryData(Player $player, string $childKey) : mixed
    {
        $xuid = $player->getXuid();
        if (!isset(self::$playerTemporaryData[$xuid])) return null;
        return self::$playerTemporaryData[$xuid][$childKey] ?? null;
    }

    /**
     * @param Player $player
     * @param string $childKey
     * @param mixed $value
     * @return void
     */
    public static function setPlayerTemporaryData(Player $player, string $childKey, mixed $value) : void
    {
        self::$playerTemporaryData[$player->getXuid()][$childKey] = $value;
    }

    /**
     * @param Player $player
     * @param string|null $childKey
     * @return void
     */
    public static function deletePlayerTemporaryData(Player $player, ?string $childKey = null) : void
    {
        $xuid = $player->getXuid();
        if (!isset(self::$playerTemporaryData[$xuid])) return;
        if ($childKey !== null && self::hasPlayerTemporaryData($player, $childKey)) {
            unset(self::$playerTemporaryData[$xuid][$childKey]);
            return;
        }
        unset(self::$playerTemporaryData[$xuid]);
    }
}