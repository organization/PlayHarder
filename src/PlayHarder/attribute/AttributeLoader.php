<?php

namespace PlayHarder\attribute;

use pocketmine\Player;
use pocketmine\Server;
use PlayHarder;
use PlayHarder\PlayHarder;
use PlayHarder\task\AutoUnloadTask;

class AttributeLoader {
	private static $instance = null;
	/**
	 *
	 * @var Users prefix data
	 */
	private $users = [ ];
	/**
	 *
	 * @var PlayHarder
	 */
	private $plugin;
	/**
	 *
	 * @var Server
	 */
	private $server;
	public function __construct(PlayHarder $plugin) {
		if (self::$instance == null)
			self::$instance = $this;
		
		$this->server = Server::getInstance ();
		$this->plugin = $plugin;
		
		$this->server->getScheduler ()->scheduleRepeatingTask ( new AutoUnloadTask ( $this ), 12000 );
	}
	/**
	 * Create a default setting
	 *
	 * @param string $userName        	
	 */
	public function loadAttribute($userName) {
		$userName = strtolower ( $userName );
		$alpha = substr ( $userName, 0, 1 );
		
		if (isset ( $this->users [$userName] ))
			return $this->users [$userName];
		
		if (! file_exists ( $this->plugin->getDataFolder () . "player/" ))
			@mkdir ( $this->plugin->getDataFolder () . "player/" );
		
		return $this->users [$userName] = new AttributeData ( $userName, $this->plugin->getDataFolder () . "player/" );
	}
	public function unloadAttribute($userName = null) {
		if ($userName === null) {
			foreach ( $this->users as $userName => $attributeData ) {
				if ($this->users [$userName] instanceof AttributeData)
					$this->users [$userName]->save ( true );
				unset ( $this->users [$userName] );
			}
			return true;
		}
		
		$userName = strtolower ( $userName );
		if (! isset ( $this->users [$userName] ))
			return false;
		if ($this->users [$userName] instanceof AttributeData) {
			$this->users [$userName]->save ( true );
		}
		unset ( $this->users [$userName] );
		return true;
	}
	/**
	 * @param Player $player
	 * @return AttributeData
	 */
	public function getAttribute(Player $player) {
		$userName = strtolower ( $player->getName () );
		if (! isset ( $this->users [$userName] ))
			$this->loadAttribute ( $userName );
		return $this->users [$userName];
	}
	public function getAttributeToName($name) {
		$userName = strtolower ( $name );
		if (! isset ( $this->users [$userName] ))
			$this->loadAttribute ( $userName );
		return $this->users [$userName];
	}
	public function save($async = false) {
		foreach ( $this->users as $userName => $attributeData )
			if ($attributeData instanceof AttributeData)
				$attributeData->save ( $async );
	}
	public static function getInstance() {
		return static::$instance;
	}
}

?>