<?php

namespace PlayHarder\system;

use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use PlayHarder\ExperienceOrb;
use pocketmine\Server;

class ExperienceSystem {
	public static function dropExpOrb(Position $source, $exp = 1, Vector3 $motion = \null, $delay = 40) {
		$newVersion = \array_map ( "intval", \explode ( ".", "2.0.0" ) );
		$apiVersion = \array_map ( "intval", \explode ( ".", Server::getInstance ()->getApiVersion () ) );
		// Completely different API version
		if ($newVersion [0] > $apiVersion [0]) {
			$this->dropExpOrb__api100 ( $source, $exp, $motion, $delay );
		} else {
			$this->dropExpOrb__api200 ( $source, $exp, $motion, $delay );
		}
	}
	public static function dropExpOrb__api100(Position $source, $exp = 1, Vector3 $motion = \null, $delay = 40) {
		$motion = $motion === \null ? new Vector3 ( \lcg_value () * 0.2 - 0.1, 0.4, \lcg_value () * 0.2 - 0.1 ) : $motion;
		$entity = Entity::createEntity ( "ExperienceOrb", $source->getLevel ()->getChunk ( $source->getX () >> 4, $source->getZ () >> 4, \true ), new \pocketmine\nbt\tag\Compound ( "", [ 
				"Pos" => new \pocketmine\nbt\tag\Enum ( "Pos", [ 
						new \pocketmine\nbt\tag\Double ( "", $source->getX () ),
						new \pocketmine\nbt\tag\Double ( "", $source->getY () ),
						new \pocketmine\nbt\tag\Double ( "", $source->getZ () ) 
				] ),
				
				"Motion" => new \pocketmine\nbt\tag\Enum ( "Motion", [ 
						new \pocketmine\nbt\tag\Double ( "", $motion->x ),
						new \pocketmine\nbt\tag\Double ( "", $motion->y ),
						new \pocketmine\nbt\tag\Double ( "", $motion->z ) 
				] ),
				"Rotation" => new \pocketmine\nbt\tag\Enum ( "Rotation", [ 
						new \pocketmine\nbt\tag\Float ( "", \lcg_value () * 360 ),
						new \pocketmine\nbt\tag\Float ( "", 0 ) 
				] ),
				"Health" => new \pocketmine\nbt\tag\Short ( "Health", 20 ),
				"PickupDelay" => new \pocketmine\nbt\tag\Short ( "PickupDelay", $delay ) 
		] ) );
		if ($entity instanceof ExperienceOrb)
			$entity->setExp ( $exp );
		$entity->spawnToAll ();
	}
	public static function dropExpOrb__api200(Position $source, $exp = 1, Vector3 $motion = \null, $delay = 40) {
		$motion = $motion === \null ? new Vector3 ( \lcg_value () * 0.2 - 0.1, 0.4, \lcg_value () * 0.2 - 0.1 ) : $motion;
		$entity = Entity::createEntity ( "ExperienceOrb", $source->getLevel ()->getChunk ( $source->getX () >> 4, $source->getZ () >> 4, \true ), new \pocketmine\nbt\tag\CompoundTag ( "", [ 
				"Pos" => new \pocketmine\nbt\tag\ListTag ( "Pos", [ 
						new \pocketmine\nbt\tag\DoubleTag ( "", $source->getX () ),
						new \pocketmine\nbt\tag\DoubleTag ( "", $source->getY () ),
						new \pocketmine\nbt\tag\DoubleTag ( "", $source->getZ () ) 
				] ),
				
				"Motion" => new \pocketmine\nbt\tag\ListTag ( "Motion", [ 
						new \pocketmine\nbt\tag\DoubleTag ( "", $motion->x ),
						new \pocketmine\nbt\tag\DoubleTag ( "", $motion->y ),
						new \pocketmine\nbt\tag\DoubleTag ( "", $motion->z ) 
				] ),
				"Rotation" => new \pocketmine\nbt\tag\ListTag ( "Rotation", [ 
						new \pocketmine\nbt\tag\FloatTag ( "", \lcg_value () * 360 ),
						new \pocketmine\nbt\tag\FloatTag ( "", 0 ) 
				] ),
				"Health" => new \pocketmine\nbt\tag\ShortTag ( "Health", 20 ),
				"PickupDelay" => new \pocketmine\nbt\tag\ShortTag ( "PickupDelay", $delay ) 
		] ) );
		if ($entity instanceof ExperienceOrb)
			$entity->setExp ( $exp );
		$entity->spawnToAll ();
	}
}

?>