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