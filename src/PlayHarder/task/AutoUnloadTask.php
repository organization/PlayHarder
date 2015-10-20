<?php

namespace PlayHarder\task;

use pocketmine\scheduler\Task;
use PlayHarder\attribute\AttributeLoader;

class AutoUnloadTask extends Task {
	protected $owner;
	public function __construct(AttributeLoader $owner) {
		$this->owner = $owner;
	}
	public function onRun($currentTick) {
		$this->owner->unloadAttribute();
	}
}
?>