<?php

namespace PlayHarder\attribute;

use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\network\protocol\UpdateAttributesPacket;
use pocketmine\entity\Attribute;
use pocketmine\Player;
use PlayHarder\system\LevelSystem;

class AttributeData {
	private $userName;
	private $dataFolder;
	private $data;
	private $server;
	public function __construct($userName, $dataFolder) {
		$userName = strtolower ( $userName );
		
		$this->userName = $userName;
		$this->dataFolder = $dataFolder . substr ( $userName, 0, 1 ) . "/";
		
		if (! file_exists ( $this->dataFolder ))
			@mkdir ( $this->dataFolder );
		
		$this->load ();
	}
	public function load() {
		$this->data = (new Config ( $this->dataFolder . $this->userName . ".json", Config::JSON, [ 
				"hunger" => 20,
				"exp" => 0,
				"expLevel" => 0,
				"expCurrent" => 0,
				"expLast" => 7,
				"expBarPercent" => 0 ] ))->getAll ();
		
		$this->server = Server::getInstance ();
	}
	public function save($async = false) {
		(new Config ( $this->dataFolder . $this->userName . ".json", Config::JSON, $this->data ))->save ( $async );
	}
	public function getHunger() {
		return $this->data ["hunger"];
	}
	public function getExp() {
		return $this->data ["exp"];
	}
	public function getExpLevel() {
		return $this->data ["expLevel"];
	}
	public function getExpCurrent() {
		return $this->data ["expCurrent"];
	}
	public function getExpLast() {
		return $This->data ["expLast"];
	}
	public function getExpBarPercent() {
		return $this->data ["expBarPercent"];
	}
	public function setHunger($hunger) {
		if ($hunger < 0)
			$hunger = 0;
		if ($hunger > 20)
			$hunger = 20;
		$this->data ["hunger"] = $hunger;
	}
	public function setExp($exp) {
		if ($exp < 0)
			$exp = 0;
		$this->data ["exp"] = $exp;
	}
	public function setExpLevel($level) {
		if ($level < 0)
			$level = 0;
		if ($level > 24791)
			$level = 24791;
		$this->data ["expLevel"] = $level;
	}
	public function setExpCurrent($current) {
		$this->data ["expCurrent"] = $current;
	}
	public function setExpLast($last) {
		$this->data ["explast"] = $last;
	}
	public function setExpBarPercent($percent) {
		if ($percent < 0)
			$percent = 0;
		if ($percent > 1)
			$percent = 1;
		$this->data ["expBarPercent"] = $percent;
	}
	public function updateAttribute() {
		$player = $this->server->getPlayer ( $this->userName );
		if ($player instanceof Player and $player->isConnected ()) {
			$pk = new UpdateAttributesPacket ();
			$pk->entityId = 0;
			
			$experience = Attribute::getAttributeByName ( "player.experience" );
			$experience->setValue ( $this->getExpBarPercent () );
			
			$experience_level = Attribute::getAttributeByName ( "player.level" );
			$experience_level->setValue ( $this->getExpLevel () );
			
			$hunger = Attribute::getAttributeByName ( "player.hunger" );
			$hunger->setValue ( $this->getHunger () );
			
			$pk->entries = [ 
					$experience,
					$experience_level,
					$hunger ];
			
			$player->dataPacket ( $pk );
		}
	}
	public function addExp($exp) {
		if ($exp == 0)
			return;
		if ($player = $this->server->getPlayer ( $this->userName ) instanceof Player)
			LevelSystem::addExp ( $player, $exp );
	}
	public function subtractExp($exp) {
		if ($exp == 0)
			return;
		if ($player = $this->server->getPlayer ( $this->userName ) instanceof Player)
			LevelSystem::subtractExp ( $player, $exp );
	}
}

?>