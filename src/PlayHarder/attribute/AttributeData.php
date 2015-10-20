<?php

namespace PlayHarder\attribute;

use pocketmine\utils\Config;

class AttributeData {
	private $userName;
	private $dataFolder;
	private $data;
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
				"exp" => 0,
				"hunger" => 20,
				"expLevel" => 0,
				"expBarPercent" => 0 
		] ))->getAll ();
	}
	public function save($async = false) {
		(new Config ( $this->dataFolder . $this->userName . ".json", Config::JSON, $this->data ))->save ( $async );
	}
	public function getExp() {
		return $this->data ["exp"];
	}
	public function getExpLevel() {
		return $this->data ["expLevel"];
	}
	public function getHunger() {
		return $this->data ["hunger"];
	}
	public function getExpBarPercent() {
		return $this->data ["expBarPercent"];
	}
	public function setExp($exp) {
		$this->data ["exp"] = $exp;
	}
	public function setExpLevel($level) {
		$this->data ["expLevel"] = $level;
	}
	public function setHunger($hunger) {
		$this->data ["hunger"] = $hunger;
		// TODO 최하값 최고값 체크
	}
	public function setExpBarPercent($percent) {
		$this->data ["expBarPercent"] = $percent;
	}
}

?>