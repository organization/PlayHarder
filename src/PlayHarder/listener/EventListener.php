<?php

namespace PlayHarder\listener;

use PlayHarder\database\PluginData;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use PlayHarder\listener\other\ListenerLoader;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Server;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\network\protocol\TakeItemEntityPacket;
use PlayHarder\ExperienceOrb;
use PlayHarder\attribute\AttributeProvider;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\BlockBreakEvent;
use PlayHarder\system\ExperienceSystem;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\entity\Attribute;

class EventListener implements Listener {
	/**
	 *
	 * @var Plugin
	 */
	private $plugin;
	private $db;
	private $listenerloader;
	private $attributeprovider;
	/**
	 *
	 * @var Server
	 */
	private $server;
	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		$this->db = PluginData::getInstance ();
		$this->listenerloader = ListenerLoader::getInstance ();
		$this->server = Server::getInstance ();
		$this->attributeprovider = AttributeProvider::getInstance ();
		
		Attribute::addAttribute ( 3, "player.hunger", 0, 20, 20 );
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $plugin );
	}
	public function registerCommand($name, $permission, $description, $usage) {
		$name = $this->db->get ( $name );
		$description = $this->db->get ( $description );
		$usage = $this->db->get ( $usage );
		$this->db->registerCommand ( $name, $permission, $description, $usage );
	}
	public function getServer() {
		return $this->server;
	}
	public function onCommand(CommandSender $player, Command $command, $label, array $args) {
		//
	}
	public function onPlayerMoveEvent(PlayerMoveEvent $event) {
		$player = $event->getPlayer ();
		
		if ($player->isSpectator ())
			return;
		
		foreach ( $player->getLevel ()->getNearbyEntities ( $player->getBoundingBox ()->grow ( 1, 0.5, 1 ), $player ) as $entity ) {
			if (! $entity->isAlive ())
				continue;
			if (! $entity instanceof ExperienceOrb)
				continue;
			
			$pk = new TakeItemEntityPacket ();
			$pk->eid = $player->getId ();
			$pk->target = $entity->getId ();
			Server::broadcastPacket ( $entity->getViewers (), $pk );
			
			$pk = new TakeItemEntityPacket ();
			$pk->eid = 0;
			$pk->target = $player->getId ();
			$player->dataPacket ( $pk );
			
			$entity->kill ();
			
			$attribute = AttributeProvider::getInstance ()->getAttribute ( $player );
			$attribute->addExp ( $entity->getExp () );
		}
	}
	public function onPlayerJoinEvent(PlayerJoinEvent $event) {
		$attribute = $this->attributeprovider->getAttribute ( $event->getPlayer () );
		$attribute->updateAttribute ();
	}
	public function onBlockBreakEvent(BlockBreakEvent $event) {
		if (mt_rand ( 1, 3 ) == 1)
			ExperienceSystem::dropExpOrb ( $event->getBlock (), $event->getBlock ()->getHardness () * 0.2 );
	}
	public function onEntityRegainHealthEvent(EntityRegainHealthEvent $event) {
		if ($event->getRegainReason () != EntityRegainHealthEvent::CAUSE_EATING)
			return;
	}
}

?>