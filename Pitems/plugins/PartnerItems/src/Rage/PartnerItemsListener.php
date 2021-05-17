<?php

namespace Rage;

use libs\utils\Utils;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use vale\items\ItemAPI;

class PartnerItemsListener implements Listener
{


	/** @var PartnerItems */
	public $plugin;

	/** @var array $strCooldown */
	public $strCooldown = [];

	/** @var array $starCooldown */
	public $starCooldown = [];

	/** @var array $boneCooldown */
	public $boneCooldown = [];

	/** @var array $noBreak */
	public $noBreak = [];

	/** @var array $effectsDisabler */
	public $effectsDisabler = [];

	/** @var array $hungerCooldown */
	public $hungerCooldown = [];

	/** @var array $comboCooldown */
	public $comboCooldown = [];

	/** @var array $comboMode */
	public $comboMode = [];

	public function __construct(PartnerItems $plugin)
	{
		$this->plugin = $plugin;
		$this->plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	public function getStrCooldown(Player $player)
	{
		if (isset($this->strCooldown[$player->getName()])) {
			$timer = time() - $this->strCooldown[$player->getName()];
		}
		return $timer;
	}

	public function getStarCooldown(Player $player)
	{
		if (isset($this->starCooldown[$player->getName()])) {
			$timer = time() - $this->strCooldown[$player->getName()];
		}
		return $timer;
	}

	public function getBoneCooldown(Player $player)
	{
		if (isset($this->boneCooldown[$player->getName()])) {
			$timer = time() - $this->boneCooldown[$player->getName()];
		}
		return $timer;
	}

	public function getNoBuildCooldown(Player $player)
	{
		if (isset($this->noBreak[$player->getName()])) {
			$timer = time() - $this->noBreak[$player->getName()];
		}
		return $timer;
	}

	public function onTouch(PlayerInteractEvent $event): void
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$inv = $player->getInventory();
		$hand = $inv->getItemInHand();
		$nbt = $hand->getNamedTag();
		$action = $event->getAction();
		if ($hand->getId() == 130 and $hand->getCustomName() == "§r§d§lPartner Package §r§f(#1030)") {
			Utils::addFireworks(new Position($player->x, $player->y + 2.5, $player->z, $player->getLevel()));
			$hand->setCount($hand->getCount() - 1);
			$inv->setItemInHand($hand);
			$event->setCancelled(true);
			$chance = rand(1, 7);
			switch ($chance) {
				case 1:
					ItemAPI::giveReward($player, ItemAPI::BONE);
					break;
				case 2:
					ItemAPI::giveReward($player, ItemAPI::NINJA_STAR);
					break;
				case 3:
					ItemAPI::giveReward($player, ItemAPI::STRENGTH2);
					break;
				case 4:
					ItemAPI::giveReward($player, ItemAPI::EFFECTS_DISABLER);
					break;
				case 5:
					ItemAPI::giveReward($player, ItemAPI::FIRE_TOOL);
					break;
				case 6:
					ItemAPI::giveReward($player, ItemAPI::HUNGER_STEALER);
					break;
				case 7:
					ItemAPI::giveReward($player, ItemAPI::COMBO);
					break;
			}
		}
		if (($action == PlayerInteractEvent::RIGHT_CLICK_AIR || $action == PlayerInteractEvent::RIGHT_CLICK_BLOCK)) {
			if ($hand->getId() == 377 && $hand->getCustomName() == "§r§c§lStrength II") {
				$event->setCancelled();
				if (!isset($this->strCooldown[$player->getName()])) {
					$hand->setCount($hand->getCount() - 1);
					$player->getInventory()->setItemInHand($hand);

					$this->strCooldown[$player->getName()] = time();
					$player->addEffect(new EffectInstance(Effect::getEffect(5), 8 * 20, 4));

				} else {
					if ((time() - $this->strCooldown[$player->getName()]) < 16) {
						$timer = time() - $this->strCooldown[$player->getName()];
						$player->getLevel()->addSound(new AnvilFallSound($player));
						$player->sendMessage("§r§cYou are on cooldown for " . "§r§c§l{$this->getStrCooldown($player)}s");
						$player->getLevel()->addSound(new AnvilFallSound(new Vector3($player->getX()), 2));
						return;
					} else {
						unset($this->strCooldown[$player->getName()]);
					}
				}

			}
		}
	}


	public function onEntityDamage(EntityDamageByEntityEvent $event)
	{
		$damager = $event->getDamager();
		$entity = $event->getEntity();
		$chance = rand(1, 5);
		if ($entity instanceof Player) {
			if ($damager instanceof Player) {
				if ($chance === 5) {
					if ($damager->getInventory()->getItemInHand()->getCustomName() === "§r§6§lVales §r§7Bone") {
						if (!isset($this->boneCooldown[$damager->getName()])) {
							if (!isset($this->noBreak[$entity->getName()]))
								$this->noBreak[$entity->getName()] = time() + 20;
							$this->boneCooldown[$damager->getName()] = time() + 40;
							$entity->sendMessage("§r§cYou have been boned by §c§l§4 " . $damager->getName());
							$damager->sendMessage("§6§lSuccesfully boned §r§7 " . $entity->getName());

						} elseif ($this->boneCooldown[$damager->getName()] <= time()) {
							unset($this->boneCooldown[$damager->getName()]);
						}

						if (isset($this->boneCooldown[$damager->getName()])) {
							$seconds = $this->boneCooldown[$damager->getName()] - time();
							$damager->sendMessage("§r§cYou cannot use this pitem for " . "§r§c§l{$seconds}s");
							$damager->getLevel()->addSound(new AnvilFallSound($damager));
							$event->setCancelled(true);
						}
					}
					if ($damager->getInventory()->getItemInHand()->getCustomName() === "§r§a§lEffects Disabler") {
						if (!isset($this->effectsDisabler[$damager->getName()])) {
							$this->effectsDisabler[$damager->getName()] = time() + 40;
							$entity->sendMessage("§r§cYou have been §r§aeffect disabled §r§c by §c§l§4 " . $damager->getName());
							$damager->sendMessage("§6§lSuccesfully §r§aeffect disabled §r§7 " . $entity->getName());
							if ($entity->hasEffects()) {
								$entity->removeAllEffects();
							}
						} elseif ($this->effectsDisabler[$damager->getName()] <= time()) {
							unset($this->effectsDisabler[$damager->getName()]);
						}
						if (isset($this->effectsDisabler[$damager->getName()])) {
							$seconds = $this->effectsDisabler[$damager->getName()] - time();
							$damager->sendMessage("§r§cYou cannot use this pitem for " . "§r§c§l{$seconds}s");
							$damager->getLevel()->addSound(new AnvilFallSound($damager));
							$event->setCancelled(true);
						}
					}
					if ($damager->getInventory()->getItemInHand()->getCustomName() === "§c§lFlint §eo §6§lFire") {
						if ($chance === 1 or 2 or 3 or 4 or 5) {
							$hand = $damager->getInventory()->getItemInHand();
							$hand->setCount($hand->getCount() - 1);
							$damager->getInventory()->setItemInHand($hand);
							$entity->setOnFire(4);
							$entity->sendMessage("§r§c§lYou were set on fire!");
							$damager->sendMessage("§r§6Succesfully set §r§7" . $entity->getName() . " §r§con fire");
						}
					}
					if ($damager->getInventory()->getItemInHand()->getCustomName() === "§r§6§lHunger Stealer") {
						if (!isset($this->hungerCooldown[$damager->getName()])) {
							$hand = $damager->getInventory()->getItemInHand();
							$hand->setCount($hand->getCount() - 1);
							$this->hungerCooldown[$damager->getName()] = time() + 20;
							$damager->getInventory()->setItemInHand($hand);
							$entity->setFood($entity->getFood() - mt_rand(1, 5));

						} elseif ($this->hungerCooldown[$damager->getName()] <= time()) {
							unset($this->hungerCooldown[$damager->getName()]);
						}
						if (isset($this->hungerCooldown[$damager->getName()])) {
							$seconds = $this->hungerCooldown[$damager->getName()] - time();
							$damager->sendMessage("§r§cYou cannot use this pitem for " . "§r§c§l{$seconds}s");
							$damager->getLevel()->addSound(new AnvilFallSound($damager));
							$event->setCancelled(true);
						}
					}
					if ($damager->getInventory()->getItemInHand()->getCustomName() === "§r§b§lCombo Ability") {
						if (!isset($this->comboCooldown[$damager->getName()])) {
							if (!isset($this->comboMode[$entity->getName()]))
								$this->comboMode[$entity->getName()] = time() + 20;
							$this->comboCooldown[$damager->getName()] = time() + 40;
							$entity->sendMessage("§r§cCombo mod on  by §c§l§4 " . $damager->getName());
							$damager->sendMessage("§6§lSuccesfully used combo on §r§7 " . $entity->getName());

						} elseif ($this->comboCooldown[$damager->getName()] <= time()) {
							unset($this->comboCooldown[$damager->getName()]);
						}

						if (isset($this->comboCooldown[$damager->getName()])) {
							$seconds = $this->comboCooldown[$damager->getName()] - time();
							$damager->sendMessage("§r§cYou cannot use this pitem for " . "§r§c§l{$seconds}s");
							$damager->getLevel()->addSound(new AnvilFallSound($damager));
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}



	/*
	 *
	 *
	 *
	 * } elseif ($this->comboCooldown[$damager->getName()] <= time()) {
								unset($this->comboCooldown[$damager->getName()]);
							}
	 */


		public function comboMode(EntityDamageByEntityEvent $event){
		 $entity = $event->getEntity();
			if (isset($this->comboMode[$entity->getName()])) {
				if ($this->comboMode[$entity->getName()] <= time()) {
					unset($this->comboMode[$entity->getName()]);
				} else {
					if (isset($this->comboMode[$entity->getName()])) {
						$event->setKnockBack(0.387);
					}
				}
			}
		}


	public function onBlockPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		if (isset($this->noBreak[$player->getName()])) {
			if ($this->noBreak[$player->getName()] <= time()) {
				unset($this->noBreak[$player->getName()]);
			} else {

				if (isset($this->noBreak[$player->getName()])) {
					$seconds = $this->noBreak[$player->getName()] - time();
					$player->sendMessage("§r§c(§c§l!§r§c) You cannot place blocks / open them for " . $seconds . "s");
					$event->setCancelled(true);
				}
			}
		}
	}

	public function onBlockBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		if (isset($this->noBreak[$player->getName()])) {
			if ($this->noBreak[$player->getName()] <= time()) {
				unset($this->noBreak[$player->getName()]);
			} else {

				if (isset($this->noBreak[$player->getName()])) {
					$seconds = $this->noBreak[$player->getName()] - time();
					$player->sendMessage("§r§c(§c§l!§r§c) You cannot place blocks / open them for " . $seconds . "s");
					$event->setCancelled(true);
				}
			}
		}
	}

	public function onNoOpen(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		if ($event->getBlock()->getId() === Block::FENCE_GATE){
			if (isset($this->noBreak[$player->getName()])) {
				if ($this->noBreak[$player->getName()] <= time()) {
					unset($this->noBreak[$player->getName()]);
				} else {

					if (isset($this->noBreak[$player->getName()])) {
						$seconds = $this->noBreak[$player->getName()] - time();
						$player->sendMessage("§r§c(§c§l!§r§c) You cannot place blocks / open them for " . $seconds . "s");
						$event->setCancelled(true);
					}
				}
			}
		}
	}
}