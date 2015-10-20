<?php

namespace PlayHarder\system;

use PlayHarder\attribute\AttributeProvider;
use pocketmine\Player;

class LevelSystem {
	private $attributeProvider;
	public $calcData = [ ];
	const EXP_LAST_INCREASE = 2;
	public function __construct() {
		$this->attributeProvider = AttributeProvider::getInstance ();
	}
	public function getExpLevel() {
		//
	}
	public function getExpBarPercent() {
		//
	}
	public function calcLevelAndExpBar(Player $player) {
		$attribute = $this->attributeProvider->getAttribute ( $player );
		
		if (isset ( $this->calcData [$player->getName ()] ))
			if ($this->calcData [$player->getName ()] ["fullExp"] == $attribute->getExp ())
				return;
		
		$level = 1;
		$expLast = 7;
		$exp;
		
		for (;;) {
			if ($expLast > $exp) {
				// 아직 레벨업전
				break;
			} else {
				$level ++;
				$expLast += self::EXP_LAST_INCREASE;
			}
		}
		
		$attribute->setExpBarPercent ( 0 ); // TODO
		$attribute->setExpLevel ( $level ); // TODO
	}
}

// TODO http://minecraft.gamepedia.com/Experience_orb

?>