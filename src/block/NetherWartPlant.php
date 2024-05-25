<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\block\utils\AgeableTrait;
use pocketmine\block\utils\BlockEventHelper;
use pocketmine\block\utils\FortuneDropHelper;
use pocketmine\block\utils\StaticSupportTrait;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use function mt_rand;

class NetherWartPlant extends Flowable{
	use AgeableTrait;
	use StaticSupportTrait;

	public const MAX_AGE = 3;


	private function canBeSupportedAt(Block $block) : bool{
		if ($block->getSide(Facing::DOWN)->getTypeId() === BlockTypeIds::SOUL_SAND) {
			return true;
		} elseif ($block->getSide(Facing::DOWN)->getTypeId() === BlockTypeIds::TUFF) {
			return true;
		} else {
			return false;
		}
	}
	

	public function ticksRandomly() : bool{
		return $this->age < self::MAX_AGE;
	}

	public function onRandomTick() : void{
		if($this->age < self::MAX_AGE) {
        	$growthChance = mt_rand(0, 10);

        	// Vérifiez si le bloc en dessous est une table de cartographie
        	$blockBelow = $this->getSide(Facing::DOWN);
        	if ($blockBelow->getTypeId() === BlockTypeIds::TUFF) {
            	// Augmentez les chances de croissance si sur une table de cartographie
            	$growthChance = mt_rand(0, 5); // Diviser par 2 pour doubler les chances de croissance
        	}

        	if ($growthChance === 0) {
            	$block = clone $this;
				$block->age++;
				BlockEventHelper::grow($this, $block, null);
        	}
    	}
	}

	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			$this->asItem()->setCount($this->age === self::MAX_AGE ? FortuneDropHelper::discrete($item, 2, 4) : 1)
		];
	}
}
