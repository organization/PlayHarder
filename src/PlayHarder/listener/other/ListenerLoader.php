<?php

namespace PlayHarder\listener\other;

use PlayHarder\listener\other\EconomyAPIListener;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

class ListenerLoader implements Listener {
	private static $instance = null;
	private $plugin;
	private $economyAPI;
	public function __construct(Plugin $plugin) {
		if (self::$instance == null)
			self::$instance = $this;
		
		$this->plugin = $plugin;
		$this->economyAPI = new EconomyAPIListener ( $plugin );
	}
	/**
	 * Get EconomyAPI plug-in instance
	 *
	 * @return \onebone\economyapi\EconomyAPI | NULL
	 */
	public function getEconomyAPI() {
		return $this->economyAPI->getEconomyAPI ();
	}
	/**
	 * Return this listenerLoader instance
	 */
	public static function getInstance() {
		return static::$instance;
	}
}
?>