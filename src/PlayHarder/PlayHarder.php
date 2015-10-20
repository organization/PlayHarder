<?php

namespace PlayHarder;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use PlayHarder\listener\EventListener;
use PlayHarder\attribute\AttributeLoader;
use PlayHarder\attribute\AttributeProvider;
use PlayHarder\listener\other\ListenerLoader;
use pocketmine\entity\Entity;
use PlayHarder\task\AutoSaveTask;
use pocketmine\utils\Config;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class PlayHarder extends PluginBase implements Listener {
	/**
	 *
	 * @var Message file
	 */
	private $messages;
	/**
	 *
	 * @var Message file version
	 */
	private $m_version = 1;
	/**
	 *
	 * @var Plug-in Instance
	 */
	private static $instance = null;
	/**
	 *
	 * @var EventListener
	 */
	private $eventListener;
	/**
	 *
	 * @var AttributeLoader
	 */
	private $attributeLoader;
	/**
	 *
	 * @var AttributeProvider
	 */
	private $attributeProvider;
	/**
	 *
	 * @var ListenerLoader
	 */
	private $listenerLoader;
	public function onEnable() {
		if (self::$instance == null)
			self::$instance = $this;
		
		if (! file_exists ( $this->getDataFolder () ))
			@mkdir ( $this->getDataFolder () );
		
		$this->initMessage ();
		
		$this->attributeLoader = new AttributeLoader ( $this );
		$this->attributeProvider = new AttributeProvider ( $this );
		$this->listenerLoader = new ListenerLoader ( $this );
		$this->eventListener = new EventListener ( $this );
		
		Entity::registerEntity ( "\\PlayHarder\\ExperienceOrb", true );
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
		
		$this->getServer ()->getScheduler ()->scheduleRepeatingTask ( new AutoSaveTask ( $this ), 12000 );
	}
	public function onDisable() {
		$this->save ();
	}
	public function save($async = false) {
		$this->attributeLoader->save ( $async );
		$this->attributeProvider->save ( $async );
	}
	public function getAttributeLoader() {
		return $this->attributeLoader;
	}
	public function getAttributeProvider() {
		return $this->attributeProvider;
	}
	public function getEventListener() {
		return $this->eventListener;
	}
	public function getListenerLoader() {
		return $this->listenerLoader;
	}
	private function messagesUpdate($targetYmlName) {
		$targetYml = (new Config ( $this->getDataFolder () . $targetYmlName, Config::YAML ))->getAll ();
		if (! isset ( $targetYml ["m_version"] )) {
			$this->saveResource ( $targetYmlName, true );
		} else if ($targetYml ["m_version"] < $this->m_version) {
			$this->saveResource ( $targetYmlName, true );
		}
	}
	public function registerCommand($name, $permission, $description = "", $usage = "") {
		$commandMap = $this->getServer ()->getCommandMap ();
		$command = new PluginCommand ( $name, $this );
		$command->setDescription ( $description );
		$command->setPermission ( $permission );
		$command->setUsage ( $usage );
		$commandMap->register ( $name, $command );
	}
	private function initMessage() {
		$this->saveResource ( "messages.yml", false );
		$this->messagesUpdate ( "messages.yml" );
		$this->messages = (new Config ( $this->getDataFolder () . "messages.yml", Config::YAML ))->getAll ();
	}
	public function get($var) {
		if (isset ( $this->messages [$this->getServer ()->getLanguage ()->getLang ()] )) {
			$lang = $this->getServer ()->getLanguage ()->getLang ();
		} else {
			$lang = "eng";
		}
		return $this->messages [$lang . "-" . $var];
	}
	public function message($player, $text = "", $mark = null) {
		if ($mark == null)
			$mark = $this->get ( "default-prefix" );
		$player->sendMessage ( TextFormat::DARK_AQUA . $mark . " " . $text );
	}
	public function alert($player, $text = "", $mark = null) {
		if ($mark == null)
			$mark = $this->get ( "default-prefix" );
		$player->sendMessage ( TextFormat::RED . $mark . " " . $text );
	}
	public static function getInstance() {
		return static::$instance;
	}
	public function onCommand(CommandSender $player, Command $command, $label, Array $args) {
		return $this->eventListener->onCommand ( $player, $command, $label, $args );
	}
}

?>