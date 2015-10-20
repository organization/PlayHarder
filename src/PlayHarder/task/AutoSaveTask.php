<?php
//
namespace PlayHarder\task;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

class AutoSaveTask extends PluginTask {
	protected $owner;
	public function __construct(Plugin $owner) {
		parent::__construct ( $owner );
	}
	public function onRun($currentTick) {
		$this->getOwner ()->save ( true );
	}
}
?>