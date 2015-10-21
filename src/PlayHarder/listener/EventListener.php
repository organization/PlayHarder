<?php

namespace PlayHarder\listener;

use PlayHarder\database\PluginData;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use PlayHarder\listener\other\ListenerLoader;
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
use pocketmine\event\player\PlayerDeathEvent;
use PlayHarder\system\LevelSystem;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\network\protocol\UpdateAttributesPacket;
use PlayHarder\system\HungerSystem;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\protocol\PlayerActionPacket;
use PlayHarder\task\HungerTask;

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
		
		$this->getServer ()->getScheduler ()->scheduleRepeatingTask ( new HungerTask ( $this ), 80 );
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
		
		if ($player->isSprinting ()) {
			HungerSystem::exhaustion ( $player, HungerSystem::SPRINTING );
		} else if ($player->isSneaking ()) {
			HungerSystem::exhaustion ( $player, HungerSystem::WALKING_AND_SNEAKING );
		}
		if ($player->isInsideOfWater ()) {
			HungerSystem::exhaustion ( $player, HungerSystem::SWIMMING );
		}
	}
	public function onPlayerJoinEvent(PlayerJoinEvent $event) {
		$attribute = $this->attributeprovider->getAttribute ( $event->getPlayer () );
		$attribute->updateAttribute ();
	}
	public function onPlayerDeathEvent(PlayerDeathEvent $event) {
		LevelSystem::setExp ( $event->getEntity (), 0 );
	}
	public function onPlayerRespawnEvent(PlayerRespawnEvent $event) {
		$attribute = $this->attributeprovider->getAttribute ( $event->getPlayer () );
		$attribute->updateAttribute ();
	}
	public function onBlockBreakEvent(BlockBreakEvent $event) {
		if (mt_rand ( 1, 5 ) == 1)
			ExperienceSystem::dropExpOrb ( $event->getBlock (), $event->getBlock ()->getHardness () * 0.2 );
		HungerSystem::exhaustion ( $event->getPlayer (), HungerSystem::BREAKING_A_BLOCK );
	}
	public function onEntityDeathEvent(EntityDeathEvent $event) {
		if ($event->getEntity ()->getLastDamageCause () instanceof EntityDamageByEntityEvent) {
			$exp = $event->getEntity ()->getMaxHealth () / 4;
			$exp += mt_rand ( 1, 3 );
			ExperienceSystem::dropExpOrb ( $event->getEntity (), $exp );
		}
	}
	public function onEntityDamageEvent(EntityDamageEvent $event) {
		if ($event instanceof EntityDamageByEntityEvent) {
			if ($event->getDamager () instanceof ExperienceOrb) {
				$event->setCancelled ();
				return;
			}
			if ($event->getDamager () instanceof Player) {
				$attribute = $this->attributeprovider->getAttribute ( $event->getDamager () );
				
				$enhance = $event->getDamage () * ($attribute->getExpLevel () / 100);
				$damage = (($event->getDamage () + $enhance) <= 15) ? ($event->getDamage () - $enhance) : 15;
				
				$event->setDamage ( $damage );
				HungerSystem::exhaustion ( $event->getDamager (), HungerSystem::RECEIVING_ANY_DAMAGE );
			}
			if ($event->getEntity () instanceof Player) {
				$attribute = $this->attributeprovider->getAttribute ( $event->getEntity () );
				
				$protect = $event->getDamage () * ($attribute->getExpLevel () / 100);
				$damage = (($event->getDamage () + $protect) >= 1) ? ($event->getDamage () - $protect) : 1;
				
				$event->setDamage ( $damage );
				HungerSystem::exhaustion ( $event->getEntity (), HungerSystem::RECEIVING_ANY_DAMAGE );
			}
		}
	}
	public function onEntityRegainHealthEvent(EntityRegainHealthEvent $event) {
		if ($event->getRegainReason () != EntityRegainHealthEvent::CAUSE_EATING)
			return;
		
		$player = $event->getEntity ();
		
		if ($player instanceof Player)
			HungerSystem::saturation ( $player, $player->getInventory ()->getItemInHand ()->getId () );
	}
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) {
		$pk = $event->getPacket ();
		
		if ($pk instanceof PlayerActionPacket) {
			if ($pk->action == PlayerActionPacket::ACTION_JUMP) {
				if ($event->getPlayer ()->isSprinting ()) {
					HungerSystem::exhaustion ( $event->getPlayer (), HungerSystem::JUMPING_WHILE_SPRINTING );
				} else if ($event->getPlayer ()->isSprinting ()) {
					HungerSystem::exhaustion ( $event->getPlayer (), HungerSystem::JUMPING );
				}
			}
			if ($pk->action == PlayerActionPacket::ACTION_START_SPRINT) {
				$attribute = AttributeProvider::getInstance ()->getAttribute ( $event->getPlayer () );
				if ($attribute->getHunger () < 6)
					$event->setCancelled ();
			}
		}
	}
	public function onDataPacketSendEvent(DataPacketSendEvent $event) {
		$pk = $event->getPacket ();
		
		if ($pk instanceof SetHealthPacket) {
			$attribute = Attribute::getAttribute ( Attribute::MAX_HEALTH );
			$attributeData = AttributeProvider::getInstance ()->getAttribute ( $event->getPlayer () );
			$attribute->setMinValue ( 0 );
			$attribute->setMaxValue ( $attributeData->getMaxHealth () );
			if ($pk->health > $attributeData->getMaxHealth ())
				$pk->health = $attributeData->getMaxHealth ();
			$attribute->setValue ( $pk->health );
			
			$attributePacket = new UpdateAttributesPacket ();
			$attributePacket->entityId = 0;
			$attributePacket->entries = [ 
					$attribute 
			];
			
			$event->setCancelled ();
			$event->getPlayer ()->dataPacket ( $attributePacket );
		}
	}
	public function hungerTick() {
		foreach ( $this->getServer ()->getOnlinePlayers () as $player ) {
			$attribute = AttributeProvider::getInstance ()->getAttribute ( $player );
			
			if ($player->getMaxHealth () > $player->getHealth ())
				if ($attribute->getHunger () == 20) {
					$ev = new EntityRegainHealthEvent ( $player, 1, EntityRegainHealthEvent::CAUSE_MAGIC );
					$player->heal ( 1, $ev );
				}
			
			if ($attribute->getHunger () == 0)
				if (($player->getHealth () - 1) < 1) {
					$ev = new EntityDamageEvent ( $player, EntityDamageEvent::CAUSE_MAGIC, $damage );
					$player->attack ( 1, $ev );
				}
		}
	}
}

?>