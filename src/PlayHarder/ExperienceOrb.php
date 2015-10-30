<?php

namespace PlayHarder;

use pocketmine\level\format\FullChunk;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\entity\Projectile;
use pocketmine\entity\Entity;

class ExperienceOrb extends Projectile {
	const NETWORK_ID = 69;
	public $width = 0.025;
	public $length = 0.025;
	public $height = 0.025;
	protected $exp = 0;
	protected $gravity = 0.024;
	protected $drag = 0.01;
	protected $dataProperties = [ 
			self::DATA_FLAGS => [ 
					self::DATA_TYPE_BYTE,
					0 ],
			self::DATA_AIR => [ 
					self::DATA_TYPE_SHORT,
					300 ],
			self::DATA_NAMETAG => [ 
					self::DATA_TYPE_STRING,
					"" ],
			self::DATA_SHOW_NAMETAG => [ 
					self::DATA_TYPE_BYTE,
					0 ],
			self::DATA_SILENT => [ 
					self::DATA_TYPE_BYTE,
					0 ],
			self::DATA_NO_AI => [ 
					self::DATA_TYPE_BYTE,
					1 ] ];
	public function __construct(FullChunk $chunk, $nbt, Entity $shootingEntity = \null) {
		parent::__construct ( $chunk, $nbt, $shootingEntity );
	}
	public function onUpdate($currentTick) {
		if ($this->closed) {
			return \false;
		}
		
		$this->timings->startTiming ();
		
		$hasUpdate = parent::onUpdate ( $currentTick );
		
		if ($this->age > 1200) {
			$this->kill ();
			$hasUpdate = \true;
		}
		
		$this->timings->stopTiming ();
		
		return $hasUpdate;
	}
	public function canCollideWith(Entity $entity){
		return ($entity instanceof Player) ? true : false ;
	}
	public function spawnTo(Player $player) {
		$pk = new AddEntityPacket ();
		$pk->type = ExperienceOrb::NETWORK_ID;
		$pk->eid = $this->getId ();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket ( $pk );
		
		parent::spawnTo ( $player );
	}
	public function getExp() {
		return $this->exp;
	}
	public function setExp($exp) {
		$this->exp = $exp;
	}
}