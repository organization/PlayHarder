<?php

namespace PlayHarder\task;

use pocketmine\scheduler\Task;
use PlayHarder\PlayHarder;
use PlayHarder\listener\EventListener;

class HungerTask extends Task {
	private $owner;
	public function __construct(EventListener $owner) {
		$this->owner = $owner;
	}
	public function onRun($currentTick) {
		$this->owner->hungerTick ();
	}
}

?>