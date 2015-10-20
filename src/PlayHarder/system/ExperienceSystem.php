<?php

namespace PlayHarder\system;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use PlayHarder\ExperienceOrb;

class ExperienceSystem {
	public static function dropExpOrb(Position $source, $exp = 1, Vector3 $motion = \null, $delay = 40) {
		$motion = $motion === \null ? new Vector3 ( \lcg_value () * 0.2 - 0.1, 0.1, \lcg_value () * 0.2 - 0.1 ) : $motion;
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
		if ($entity instanceof ExperienceOrb)
			$entity->setExp ( $exp );
		$entity->spawnToAll ();
	}
}

?>