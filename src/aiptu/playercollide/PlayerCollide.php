<?php

/*
 * Copyright (c) 2025 AIPTU
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/AIPTU/PlayerCollide
 */
 
declare(strict_types=1);

namespace aiptu\playercollide;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase;

/**
 * @no-named-arguments
 */
class PlayerCollide extends PluginBase implements Listener {
	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @priority LOW
	 */
	public function onPlayerMove(PlayerMoveEvent $event) : void {
		$player = $event->getPlayer();
		$from = $event->getFrom();
		$to = $event->getTo();

		$collisionDistance = (float) $this->getConfig()->get('collision_distance', 0.8);
		$baseKnockbackStrength = (float) $this->getConfig()->get('knockback_strength', 0.1);
		$speedKnockbackMultiplier = (float) $this->getConfig()->get('speed_knockback_multiplier', 0.5);

		foreach ($player->getViewers() as $viewer) {
			if ($viewer->isSpectator()) {
				continue;
			}

			$distance = $player->getPosition()->distance($viewer->getPosition());

			if ($distance <= $collisionDistance) {
				$direction = $player->getPosition()->subtractVector($viewer->getPosition())->normalize();
				$playerSpeed = $from->distance($to);
				$knockbackValue = $baseKnockbackStrength + ($playerSpeed * $speedKnockbackMultiplier);

				$player->knockBack(
					$direction->x,
					$direction->z,
					$knockbackValue,
					0.0
				);

				$viewer->knockBack(
					-$direction->x,
					-$direction->z,
					$knockbackValue,
					0.0
				);
			}
		}
	}
}
