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
use pocketmine\entity\Attribute;

class EventListener implements Listener {
	/**
	 *
	 * @var Plugin
	 */
	private $plugin;
	private $db;
	private $listenerloader;
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
		// TODO - 명령어처리용
		if (strtolower ( $command ) == $this->db->get ( "" )) { // TODO <- 빈칸에 명령어
			if (! isset ( $args [0] )) {
				// TODO - 명령어만 쳤을경우 도움말 표시
				return true;
			}
			switch (strtlower ( $args [0] )) {
				case $this->db->get ( "" ) :
					// TODO ↗ 빈칸에 세부명령어
					// TODO 세부명령어 실행시 원하는 작업 실행
					break;
				case $this->db->get ( "" ) :
					// TODO ↗ 빈칸에 세부명령어
					// TODO 세부명령어 실행시 원하는 작업 실행
					break;
				default :
					// TODO - 잘못된 명령어 입력시 도움말 표시
					break;
			}
			return true;
		}
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
			
			$entity->kill (); // TODO 해당유저 경험치 상승
		}
	}
	public function onPlayerJoinEvent(PlayerJoinEvent $event) {
		$pk = new UpdateAttributesPacket ();
		$pk->entityId = 0;
		
		$experience = Attribute::getAttribute ( Attribute::EXPERIENCE );
		$experience->setValue ( 1 );
		
		$experience_level = Attribute::getAttribute ( Attribute::EXPERIENCE_LEVEL );
		$experience_level->setValue ( 24791 );
		
		$hunger = Attribute::addAttribute ( 3, "player.hunger", 0, 20, 20 );
		$hunger->setValue ( 4 );
		
		$pk->entries = [ 
				$experience,
				$experience_level,
				$hunger 
		];
		
		$event->getPlayer ()->dataPacket ( $pk );
	}
	public function onPlayerInteractEvent(PlayerInteractEvent $event) {
		$player = $event->getPlayer ();
		$block = $event->getBlock ();
		//$this->dropExpOrb ( $block );
	}
}

?>