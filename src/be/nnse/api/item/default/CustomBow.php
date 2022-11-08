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
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Arrow as ArrowEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\BowShootSound;

abstract class CustomBow extends Bow implements ICustomItem
{
    /**
     * @param string $customName
     * @param array $lore
     */
    public function __construct(
        string $customName = "",
        array $lore = []
    )
    {
        parent::__construct(new ItemIdentifier(ItemIds::BOW, 0), TextFormat::clean($customName));

        if ($customName !== "") {
            if ($customName === TextFormat::clean($customName)) {
                $this->setCustomName(TextFormat::RESET . $customName);
            } else {
                $this->setCustomName($customName);
            }
        }
        if (!empty($lore)) $this->setLore($lore);
    }

    public function onReleaseUsing(Player $player) : ItemUseResult
    {
        $source = $this->getSourceItem();
        $inventory = match(true) {
            $player->getOffHandInventory()->contains($source) => $player->getOffHandInventory(),
            $player->getInventory()->contains($source) => $player->getInventory(),
            default => null
        };

        if ($player->hasFiniteResources() && $inventory === null) {
            return ItemUseResult::FAIL();
        }

        $location = $player->getLocation();

        $diff = $player->getItemUseDuration();
        $p = $diff / 20;
        $baseForce = min((($p ** 2) + $p * 2) / 3, 1);

        $ejectLocation = Location::fromObject(
            $player->getEyePos(),
            $player->getWorld(),
            ($location->yaw > 180 ? 360 : 0) - $location->yaw,
            -$location->pitch
        );
        $entity = $this->getSourceEntity($ejectLocation, $player, $baseForce >= 1);
        $entity->setMotion($player->getDirectionVector());

        $ev = new EntityShootBowEvent($player, $this, $entity, $baseForce * 3);

        if ($baseForce < 0.1 || $diff < 5 || $player->isSpectator()) {
            $ev->cancel();
        }
        $ev->call();

        $entity = $ev->getProjectile();

        if($ev->isCancelled()){
            $entity->flagForDespawn();
            return ItemUseResult::FAIL();
        }

        $entity->setMotion($entity->getMotion()->multiply($ev->getForce()));

        if ($entity instanceof Projectile) {
            $projectileEv = new ProjectileLaunchEvent($entity);
            $projectileEv->call();
            if ($projectileEv->isCancelled()) {
                $ev->getProjectile()->flagForDespawn();
                return ItemUseResult::FAIL();
            }

            $ev->getProjectile()->spawnToAll();
            $location->getWorld()->addSound($location, new BowShootSound());
        }else{
            $entity->spawnToAll();
        }

        if($player->hasFiniteResources()){
            if (!$this->hasEnchantment(VanillaEnchantments::INFINITY())) {
                $inventory?->removeItem($source);
            }
            $this->applyDamage(1);
        }

        return ItemUseResult::SUCCESS();
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canStartUsingItem(Player $player) : bool
    {
        $source = $this->getSourceItem();
        $hasFiniteResource = $player->hasFiniteResources();
        $hasInventory = $player->getInventory()->contains($source);
        $hasOffHandInventory = $player->getOffHandInventory()->contains($source);
        return !$hasFiniteResource || $hasInventory || $hasOffHandInventory;
    }

    /**
     * @return Item
     */
    protected function getSourceItem() : Item
    {
        return VanillaItems::ARROW();
    }

    /**
     * @param Location $ejectLocation
     * @param Entity $shooter
     * @param bool $critical
     * @return Projectile
     */
    protected function getSourceEntity(Location $ejectLocation, Entity $shooter, bool $critical) : Projectile
    {
        $arrow = new ArrowEntity($ejectLocation, $shooter, $critical);

        $infinity = $this->hasEnchantment(VanillaEnchantments::INFINITY());
        if ($infinity) {
            $arrow->setPickupMode(ArrowEntity::PICKUP_CREATIVE);
        }
        if (($punchLevel = $this->getEnchantmentLevel(VanillaEnchantments::PUNCH())) > 0) {
            $arrow->setPunchKnockback($punchLevel);
        }
        if (($powerLevel = $this->getEnchantmentLevel(VanillaEnchantments::POWER())) > 0) {
            $arrow->setBaseDamage($arrow->getBaseDamage() + (($powerLevel + 1) / 2));
        }
        if ($this->hasEnchantment(VanillaEnchantments::FLAME())) {
            $arrow->setOnFire(intdiv($arrow->getFireTicks(), 20) + 100);
        }

        return $arrow;
    }
}