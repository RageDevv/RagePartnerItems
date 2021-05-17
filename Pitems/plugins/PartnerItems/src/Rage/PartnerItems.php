<?php

declare(strict_types=1);

namespace Rage;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use vale\PartnerItemsListener;

final class PartnerItems extends PluginBase
{
	public $commands = ["pi", "pp", "package"];

	public function onEnable(): void
	{
		new PartnerItemsListener($this);

	}

	public function onDisable()
	{

	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
	{
		if ($sender instanceof Player) {
			switch ($command->getName()) {
				case "package":
					if (count($args) < 2) {
						$sender->sendMessage("/package <player> <amount>");
					} elseif (($player = Server::getInstance()->getPlayer($args[0])) && is_numeric($args[1])) {
						$item = Item::get(Item::ENDER_CHEST, 0 , (int) $args[1]);
						$item->setCustomName("§r§d§lPartner Package §r§f(#1030)");
						$item->setLore([
							'§r§7Right click to open a partner package'
						]);
						$player->getInventory()->addItem($item);

						$sender->sendMessage("§r§b(§l!§r§b) You have Succesfully gaven {$player->getName()} {$args[1]} partnerpackages");
					}


					break;
			}
		}
		return true;
	}

	public function sendUsage(Player $player): array
	{
		$message = [
			"§r§l§bPARTNER PACKAGE HELP",
			"§r§7 - §r§b/package give <player> <amount>",

		];
		return $message;
	}

}
