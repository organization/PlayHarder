<?php

namespace PlayHarder\system;

use PlayHarder\attribute\AttributeProvider;
use pocketmine\item\Item;
use pocketmine\Player;

class HungerSystem {
	private $attributeProvider;
	const WALKING_AND_SNEAKING = 0.01;
	const SWIMMING = 0.015;
	const BREAKING_A_BLOCK = 0.025;
	const SPRINTING = 0.1;
	const JUMPING = 0.2;
	const ATTACKING_AN_ENEMY = 0.3;
	const RECEIVING_ANY_DAMAGE = 0.3;
	const JUMPING_WHILE_SPRINTING = 0.8;
	public function __construct() {
		$this->attributeProvider = AttributeProvider::getInstance ();
	}
	public function exhaustion(Player $player, int $point) {
		$attribute = $this->attributeProvider->getAttribute ( $player );
		$attribute->setHunger ( $attribute->getHunger () - $point );
	}
	public function saturation(Player $player, int $itemId) {
		switch ($itemId) {
			case Item::APPLE :
				$point = 4;
				break;
			case Item::BAKED_POTATO :
				$point = 5;
				break;
			case Item::BEETROOT :
				$point = 1;
				break;
			case Item::BEETROOT_SOUP :
				$point = 6;
				break;
			case Item::BREAD :
				$point = 5;
				break;
			case Item::CAKE :
			case Item::CAKE_BLOCK :
				$point = 2;
				break;
			case Item::CARROT :
			case Item::CARROT_BLOCK :
			case Item::CARROTS :
				$point = 3;
				break;
			case Item::COOKED_CHICKEN :
				$point = 6;
				break;
			case Item::COOKED_FISH :
				$point = 5;
				break;
			case Item::COOKIE :
				$point = 2;
				break;
			case Item::GOLDEN_APPLE :
				$point = 4;
				break;
			case Item::MELON :
				$point = 2;
				break;
			case Item::MUSHROOM_STEW :
				$point = 6;
				break;
			case Item::POTATO :
				$point = 1;
				break;
			case Item::PUMPKIN_PIE :
				$point = 8;
				break;
			case Item::RAW_BEEF :
				$point = 3;
				break;
			case Item::RAW_CHICKEN :
				$point = 2;
				break;
			case Item::RAW_FISH :
				$point = 2;
				break;
			case Item::STEAK :
				$point = 8;
				break;
			default :
				$point = 0;
				break;
			// DATA http://minecraft.gamepedia.com/Hunger
		}
		
		$attribute = $this->attributeProvider->getAttribute ( $player );
		$attribute->setHunger ( $attribute->getHunger () + $point );
	}
}

?>