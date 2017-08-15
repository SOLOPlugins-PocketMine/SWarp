<?php

namespace solo\swarp;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class TitleManager implements Listener{

  private $players = [];

  public function __construct(SWarp $owner){
    $this->owner = $owner;

    $this->owner->getServer()->getPluginManager()->registerEvents($this, $this->owner);

    $this->owner->getServer()->getScheduler()->scheduleRepeatingTask(new class($this->owner) extends SWarpTask{
      public function _onRun(int $currentTick){
        $this->owner->getTitleManager()->tick();
      }
    }, 2);
  }

  public function addTitle(Player $player, string $message){
    if(!isset($this->players[$player->getName()])){
      $this->players[$player->getName()] = [];
    }
    $this->players[$player->getName()]["title"] = $message;
  }

  public function addSubTitle(Player $player, string $message){
    if(!isset($this->players[$player->getName()])){
      $this->players[$player->getName()] = [];
    }
    $this->players[$player->getName()]["subTitle"] = $message;
  }

  public function tick(){
    foreach($this->players as $player => $titles){
      $playerInstance = $this->owner->getServer()->getPlayerExact($player);
      if($playerInstance === null){
        continue;
      }
      $playerInstance->addTitle($titles["title"] ?? "", $titles["subTitle"] ?? "");
    }
    $this->players = [];
  }

  public function handlePlayerQuit(PlayerQuitEvent $event){
    unset($this->players[$event->getPlayer()->getName()]);
  }
}
