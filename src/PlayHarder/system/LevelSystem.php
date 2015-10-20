<?php

namespace PlayHarder\system;

use PlayHarder\attribute\AttributeProvider;
use pocketmine\Player;

class LevelSystem {
	private $attributeProvider;
	public function __construct() {
		$this->attributeProvider = AttributeProvider::getInstance ();
	}
	public function addExp(Player $player, $exp) {
		$attribute = $this->attributeProvider->getAttribute ( $player );
		
		$exp = $attribute->getExp () + $exp;
		$last = $attribute->getExpLast ();
		$current = $attribute->getExpCurrent ();
		$level = $attribute->getExpLevel ();
		$percent = $attribute->getExpBarPercent ();
		
		for(;;) {
			if ($last > $exp) {
				$current = $last - $exp;
				$percent = sprintf ( "%.1f", $current / $last );
				break;
			} else {
				$level ++;
				$last += 2;
			}
		}
		
		$attribute->setExp ( $exp );
		$attribute->setExpLast ( $last );
		$attribute->setExpCurrent ( $current );
		$attribute->setExpLevel ( $level );
		$attribute->setExpBarPercent ( $percent );
		$attribute->updateAttribute ();
	}
	public function subtractExp(Player $player, $exp) {
		$attribute = $this->attributeProvider->getAttribute ( $player );
		$this->setExp ( $player, $attribute->getExp () - $exp );
	}
	public function setExp(Player $player, $exp) {
		$attribute = $this->attributeProvider->getAttribute ( $player );
		
		if ($attribute->getExp () == $exp)
			return;
		
		$level = 1;
		$last = 7;
		
		for(;;) {
			if ($last > $exp) {
				$current = $last - $exp;
				$percent = sprintf ( "%.1f", $current / $last );
				break;
			} else {
				$level ++;
				$last += 2;
			}
		}
		
		$attribute->setExp ( $exp );
		$attribute->setExpLast ( $last );
		$attribute->setExpCurrent ( $current );
		$attribute->setExpLevel ( $level );
		$attribute->setExpBarPercent ( $percent );
		$attribute->updateAttribute ();
	}
}

// Reference http://minecraft.gamepedia.com/Experience_orb

?>