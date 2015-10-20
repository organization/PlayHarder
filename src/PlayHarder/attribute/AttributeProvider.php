<?php

namespace PlayHarder\attribute;

use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;
use PlayHarder;

class AttributeProvider {
	/**
	 *
	 * @var AttributeProvider
	 */
	private static $instance = null;
	/**
	 *
	 * @var \PlayHarder\PlayHarder
	 */
	private $plugin;
	/**
	 *
	 * @var AttributeLoader
	 */
	private $loader;
	/**
	 *
	 * @var Server
	 */
	private $server;
	/**
	 *
	 * @var AttributeProvider DB
	 */
	private $db;
	public function __construct(PlayHarder $plugin) {
		if (self::$instance == null)
			self::$instance = $this;
		
		$this->plugin = $plugin;
		$this->loader = $plugin->getAttributeLoader ();
		$this->server = Server::getInstance ();
		
		$this->db = (new Config ( $this->plugin->getDataFolder () . "pluginDB.yml", Config::YAML, [ ] ))->getAll ();
	}
	public function save($async = false) {
		(new Config ( $this->plugin->getDataFolder () . "pluginDB.yml", Config::YAML, $this->db ))->save ( $async );
	}
	public function loadAttribute($userName) {
		return $this->loader->loadAttribute ( $userName );
	}
	public function unloadAttribute($userName = null) {
		return $this->loader->unloadAttribute ( $userName );
	}
	/**
	 *
	 * @param Player $player        	
	 * @return AttributeData
	 */
	public function getAttribute(Player $player) {
		return $this->loader->getAttribute ( $player );
	}
	public function getAttributeToName($name) {
		return $this->loader->getAttributeToName ( $name );
	}
	public static function getInstance() {
		return static::$instance;
	}
}

?>