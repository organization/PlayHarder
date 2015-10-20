<?php

namespace PlayHarder\system;

use PlayHarder\attribute\AttributeProvider;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\Player;

class ExperienceSystem {
	private $attributeProvider;
	public $expOrbs = [ ];
	public function __construct() {
		$this->attributeProvider = AttributeProvider::getInstance ();
	}
	public function getExpOrb($eid) {
		return isset ( $this->expOrbs [$eid] ) ? $this->expOrbs [$eid] : 0;
	}
	public function useExpOrb(Player $player, $eid) {
		$attribute = $this->attributeProvider->getAttribute ( $player );
		$attribute->addExp ( $player, $this->getExpOrb ( $eid ) );
		
		if (isset ( $this->expOrbs [$eid] ))
			unset ( $this->expOrbs [$eid] );
	}
	public function dropExpOrb(Position $source, $exp = 1, Vector3 $motion = \null, $delay = 40) {
		$motion = $motion === \null ? new Vector3 ( \lcg_value () * 0.2 - 0.1, 0.2, \lcg_value () * 0.2 - 0.1 ) : $motion;
		$entity = Entity::createEntity ( "ExperienceOrb", $source->getLevel ()->getChunk ( $source->getX () >> 4, $source->getZ () >> 4, \true ), new CompoundTag ( "", [ 
				"Pos" => new ListTag ( "Pos", [ 
						new DoubleTag ( "", $source->getX () ),
						new DoubleTag ( "", $source->getY () + 2 ),
						new DoubleTag ( "", $source->getZ () ) ] ),
				
				"Motion" => new ListTag ( "Motion", [ 
						new DoubleTag ( "", $motion->x ),
						new DoubleTag ( "", $motion->y ),
						new DoubleTag ( "", $motion->z ) ] ),
				"Rotation" => new ListTag ( "Rotation", [ 
						new FloatTag ( "", \lcg_value () * 360 ),
						new FloatTag ( "", 0 ) ] ),
				"Health" => new ShortTag ( "Health", 20 ),
				"PickupDelay" => new ShortTag ( "PickupDelay", $delay ) ] ) );
		$this->expOrbs [$entity->getId ()] = $exp;
		$entity->spawnToAll ();
	}
}

?>