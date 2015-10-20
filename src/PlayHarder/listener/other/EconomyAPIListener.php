<?php

namespace PlayHarder\listener\other;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use onebone\economyapi\event\money\AddMoneyEvent;
use onebone\economyapi\event\money\ReduceMoneyEvent;
use onebone\economyapi\event\money\SetMoneyEvent;
use onebone\economyapi\event\bank\MoneyChangedEvent;

class EconomyAPIListener implements Listener {
	private $economyAPI = null;
	public function __construct(Plugin $plugin) {
		$server = Server::getInstance ();
		if ($server->getPluginManager ()->getPlugin ( "EconomyAPI" ) != null) {
			$this->economyAPI = \onebone\economyapi\EconomyAPI::getInstance ();
			$server->getPluginManager ()->registerEvents ( $this, $plugin );
		}
	}
	/**
	 * Get EconomyAPI plug-in instance
	 *
	 * @return \onebone\economyapi\EconomyAPI | NULL
	 */
	public function getEconomyAPI() {
		return $this->economyAPI;
	}
	// TODO 아래이벤트를 정의하지 않는다면 모두 지우셔도 됩니다
	// -------------------------------------------------------------
	public function onAddMoneyEvent(AddMoneyEvent $event) {
		// TODO 유저에게 돈울 추가될때 이 곳에 정의된 작업실행
	}
	public function onReduceMoneyEvent(ReduceMoneyEvent $event) {
		// TODO 유저에게 돈울 뺏을때 이 곳에 정의된 작업실행
	}
	public function onSetMoneyEvent(SetMoneyEvent $event) {
		// TODO 유저의 돈을 설정했을때 정의된 작업실행
	}
	public function onMoneyChangedEvent(MoneyChangedEvent $event) {
		// TODO 유저가 소유한 돈이 바뀔때 반드시 실행
	}
	// -------------------------------------------------------------
}

?>