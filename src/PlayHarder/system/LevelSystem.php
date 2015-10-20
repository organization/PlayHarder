<?php

namespace PlayHarder\system;

use PlayHarder\attribute\AttributeProvider;
use pocketmine\Player;

class LevelSystem {
	public static function addExp(Player $player, $exp) {
		if ($exp == 0)
			return;
		
		$attribute = AttributeProvider::getInstance()->getAttribute ( $player );
		
		$exp = $attribute->getExp () + $exp;
		$current = $attribute->getExpCurrent () + $exp;
		$last = $attribute->getExpLast ();
		$level = $attribute->getExpLevel ();
		$percent = $attribute->getExpBarPercent ();
		
		for(;;) {
			if ($last > $current) {
				$percent = sprintf ( "%.1f", $current / $last );
				break;
			} else {
				$level ++;
				$current = 0;
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
	public static function subtractExp(Player $player, $exp) {
		if ($exp == 0)
			return;
		
		$attribute = AttributeProvider::getInstance()->getAttribute ( $player );
		$this->setExp ( $player, $attribute->getExp () - $exp );
	}
	public static function setExp(Player $player, $exp) {
		$attribute = AttributeProvider::getInstance()->getAttribute ( $player );
		
		if ($attribute->getExp () == $exp)
			return;
		
		$level = 1;
		$last = 7;
		$current = $exp;
		
		for(;;) {
			if ($last > $current) {
				$percent = sprintf ( "%.1f", $current / $last );
				break;
			} else {
				$level ++;
				$last += 2;
				$current = 0;
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