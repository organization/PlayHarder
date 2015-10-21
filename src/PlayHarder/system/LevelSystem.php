<?php

namespace PlayHarder\system;

use PlayHarder\attribute\AttributeProvider;
use pocketmine\Player;

class LevelSystem {
	public static function addExp(Player $player, $add) {
		if ($add == 0)
			return;
		
		$attribute = AttributeProvider::getInstance ()->getAttribute ( $player );
		
		$exp = $attribute->getExp () + $add;
		$current = $attribute->getExpCurrent () + $add;
		$last = $attribute->getExpLast ();
		$level = $attribute->getExpLevel ();
		$percent = $attribute->getExpBarPercent ();
		
		for(;;) {
			if ($last > $current) {
				$percent = sprintf ( "%.1f", $current / $last );
				break;
			} else {
				if ($level == 0) {
					$level = 1;
					$last = 7;
				} else {
					$level ++;
					$last += 2;
				}
				$current -= $last;
				$attribute->addMaxHealth ();
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
		
		$attribute = AttributeProvider::getInstance ()->getAttribute ( $player );
		$this->setExp ( $player, $attribute->getExp () - $exp );
	}
	public static function setExp(Player $player, $exp) {
		$attribute = AttributeProvider::getInstance ()->getAttribute ( $player );
		
		if ($attribute->getExp () == $exp)
			return;
		
		$level = 0;
		$last = 0;
		$current = $exp;
		
		for(;;) {
			if ($last > $current) {
				$percent = sprintf ( "%.1f", $current / $last );
				break;
			} else {
				if ($level == 0) {
					$level = 1;
					$current -= $last;
					$last = 7;
				} else {
					$level ++;
					$current -= $last;
					$last += 2;
				}
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