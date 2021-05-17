<?php

namespace Rage\items;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use libs\utils\Utils;

final class ItemAPI
{

	const BONE = 1;
	const NINJA_STAR = 2;
	const STRENGTH2 = 3;
	const EFFECTS_DISABLER = 4;
	const FIRE_TOOL = 5;
	const HUNGER_STEALER = 6;
	const COMBO = 7;

	public static function giveReward(Player $player, int $id)
	{
		switch ($id) {
			case self::BONE:
				$bone = Item::get(Item::BONE, 0, mt_rand(1, 3))->
				setCustomName("§r§6§lVales §r§7Bone")->
				setLore([
					'§r§7Hit a player with this bone 3 times to prevent them from building',
				]);
				$bone->getNamedTag()->setTag(new StringTag("BonePartnerItem"));
				$player->getInventory()->addItem($bone);
				$player->sendMessage("§r§6§l* §r§7((x §r§61 §r§6§lVales §r§7Bone §r§7))");

				break;

			case self::NINJA_STAR:
				$star = Item::get(Item::NETHER_STAR, 0, rand(1,3))->setCustomName("§r§5§lMeezoids §r§7Ninja Star")->setLore([
					'§r§5§lClick §r§7to teleport to your last §r§5§lattacker in §r§710 seconds'
				]);
				$star->getNamedTag()->setTag(new StringTag("partnerstar"));
				$player->getInventory()->addItem($star);
				$player->sendMessage("§r§6§l* §r§7((x §r§61 §r§5§lMeezoids §r§7Ninja Star §r§7))");

				break;

			case self::STRENGTH2:
				$strength = Item::get(Item::BLAZE_POWDER, 0, mt_rand(1, 3))->
				setCustomName("§r§c§lStrength II")->
				setLore([
					'§r§7Tap this to §r§c§lRecieve §r§7Strength II for 5 seconds',
				]);
				$strength->getNamedTag()->setTag(new StringTag("BonePartnerItem"));

				$player->getInventory()->addItem($strength);
				$player->sendMessage("§r§6§l* §r§7((x §r§6{$strength->getCount()} §r§c§l{$strength->getCustomName()}§r§7))");
				break;

			case self::EFFECTS_DISABLER:
				$effects = Item::get(Item::SLIME_BALL, 0, mt_rand(1,3))->
				setCustomName("§r§a§lEffects Disabler")->
				setLore([
					'§r§7Hit a player with this item 3 times',
					'§r§7to remove §r§l§4 all their §r§7§aEffects.',
				]);
				$player->getInventory()->addItem($effects);
				$player->sendMessage("§r§6§l* §r§7((x §r§6{$effects->getCount()} §r§c§l{$effects->getCustomName()}§r§7))");
				break;
			case self::FIRE_TOOL:
				$fire = Item::get(Item::FLINT_AND_STEEL, 0,  mt_rand(1,3))->
				setCustomName("§c§lFlint §eo §6§lFire")->setLore([
					'§r§7Hit a player with this item to',
					'§r§cset §r§7them on §r§4fire for 5 seconds.'
				]);
				$fire->setDamage(64);
				$player->getInventory()->addItem($fire);
				$player->sendMessage("§r§6§l* §r§7((x §r§6{$fire->getCount()} §r§c§l{$fire->getCustomName()}§r§7))");
				break;
			case self::HUNGER_STEALER:
				$hunger = Item::get(Item::ROTTEN_FLESH, 0, mt_rand(1,3))->
				setCustomName("§r§6§lHunger Stealer")->
				setLore([
					'§r§7Hit a player with this item to remove',
					'§r§6§l3-5 §r§7hunger bars!',
				]);
				$player->getInventory()->addItem($hunger);
				$player->sendMessage("§r§6§l* §r§7((x §r§6{$hunger->getCount()} §r§c§l{$hunger->getCustomName()}§r§7))");
				break;
			case self::COMBO:
				$combo = Item::get(Item::PUFFERFISH, 0, mt_rand(1,3))->
				setCustomName("§r§b§lCombo Ability")->setLore([
					'§r§7Hit a player with this item to',
					'§r§b§lMake them §r§7take §r§b§lCombo Knockback!'
				]);
				$player->getInventory()->addItem($combo);
				$player->sendMessage("§r§6§l* §r§7((x §r§6{$combo->getCount()} §r§c§l{$combo->getCustomName()}§r§7))");

				break;
		}
	}
}