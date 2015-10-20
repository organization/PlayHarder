<?php

namespace PlayHarder\database;

use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;

class PluginData {
	private static $instance = null;
	/**
	 *
	 * @var Server
	 */
	private $server;
	/**
	 *
	 * @var Plugin
	 */
	private $plugin;
	public $messages, $db;
	public $m_version = 1;
	/**
	 * Initialize the plug-in database
	 *
	 * @param Plugin $plugin        	
	 * @param string $messageVersion        	
	 * @param array $defaultDB        	
	 */
	public function __construct(Plugin $plugin, $messageVersion = null, $defaultDB = []) {
		if (self::$instance == null)
			self::$instance = $this;
		
		$this->plugin = $plugin;
		$this->server = Server::getInstance ();
		$this->messages = $this->initMessage ();
		$this->db = $this->initDatabase ();
	}
	/**
	 * Register the plug-in command
	 *
	 * @param string $name        	
	 * @param string $permission        	
	 * @param string $description        	
	 * @param string $usage        	
	 */
	public function registerCommand($name, $permission, $description = "", $usage = "") {
		$commandMap = $this->getServer ()->getCommandMap ();
		$command = new PluginCommand ( $name, $this->plugin );
		$command->setDescription ( $description );
		$command->setPermission ( $permission );
		$command->setUsage ( $usage );
		$commandMap->register ( $name, $command );
	}
	/**
	 * Gets a translated message
	 *
	 * @param string $var        	
	 */
	public function get($var) {
		if (isset ( $this->messages [$this->getServer ()->getLanguage ()->getLang ()] )) {
			$lang = $this->getServer ()->getLanguage ()->getLang ();
		} else {
			$lang = "eng";
		}
		return $this->messages [$lang . "-" . $var];
	}
	/**
	 * Print the message
	 *
	 * @param CommandSender $player        	
	 * @param string $text        	
	 * @param string $mark        	
	 */
	public function message(CommandSender $player, $text = "", $mark = null) {
		if ($mark == null)
			$mark = $this->get ( "default-prefix" );
		$player->sendMessage ( TextFormat::DARK_AQUA . $mark . " " . $text );
	}
	/**
	 * Print the alert
	 *
	 * @param CommandSender $player        	
	 * @param string $text        	
	 * @param string $mark        	
	 */
	public function alert(CommandSender $player, $text = "", $mark = null) {
		if ($mark == null)
			$mark = $this->get ( "default-prefix" );
		$player->sendMessage ( TextFormat::RED . $mark . " " . $text );
	}
	/**
	 * Save the message file to the server
	 */
	public function initMessage() {
		$this->getPlugin ()->saveResource ( "messages.yml", false );
		$this->messagesUpdate ( "messages.yml" );
		return (new Config ( $this->getPlugin ()->getDataFolder () . "messages.yml", Config::YAML ))->getAll ();
	}
	/**
	 * Save the database file (.json) to the server
	 */
	public function initDatabase() {
		@mkdir ( $this->getPlugin ()->getDataFolder () );
		return (new Config ( $this->getPlugin ()->getDataFolder () . "pluginDB.json", Config::JSON, [ ] ))->getAll ();
	}
	/**
	 * Updating the message file stored at the server
	 *
	 * @param string $targetYmlName        	
	 */
	public function messagesUpdate($targetYmlName) {
		$targetYml = (new Config ( $this->getPlugin ()->getDataFolder () . $targetYmlName, Config::YAML ))->getAll ();
		if (! isset ( $targetYml ["m_version"] )) {
			$this->getPlugin ()->saveResource ( $targetYmlName, true );
		} else if ($targetYml ["m_version"] < $this->m_version) {
			$this->getPlugin ()->saveResource ( $targetYmlName, true );
		}
	}
	/**
	 * Save plug-in database
	 *
	 * @param boolean $async        	
	 */
	public function save($async = false) {
		$save = new Config ( $this->getPlugin ()->getDataFolder () . "pluginDB.json", Config::JSON );
		$save->setAll ( $this->db );
		$save->save ( $async );
	}
	/**
	 * Return the server instance
	 *
	 * @return Server
	 */
	private function getServer() {
		return $this->server;
	}
	/**
	 * Return the plug-in instance
	 */
	private function getPlugin() {
		return $this->plugin;
	}
	/**
	 * Return this plug-in database instance
	 */
	public static function getInstance() {
		return static::$instance;
	}
}

?>