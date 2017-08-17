<?php

namespace solo\swarp;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\DataPacketSendEvent;

class ShortcutManager implements Listener{

  public function __construct(SWarp $owner){
    $this->owner = $owner;

    $this->owner->getServer()->getPluginManager()->registerEvents($this, $this->owner);
  }

  public function updateShortcut(){
    foreach($this->owner->getServer()->getOnlinePlayers() as $player){
      $player->sendCommandData();
    }
  }

  public function handlePlayerCommandPreprocess(PlayerCommandPreprocessEvent $event){
    $text = $event->getMessage();
    if(substr($text, 0, 1) === '/'){
      $find = substr($text, 1);
      $warp = $this->owner->getWarp($find);
      if($warp !== null && $warp->containsOption("쇼트컷")){
        $event->setMessage('/워프 ' . $find); // via warp command
      }
    }
  }

  public function handleDataPacketSend(DataPacketSendEvent $event){
    if($event->getPacket()->pid() === 0x4e){ // AVAILABLE_COMMANDS_PACKET
      $commands = json_decode($event->getPacket()->commands, true);
      foreach($this->owner->getAllWarp() as $warp){
        if(
          !isset($commands[$warp->getName()])
          && $warp->containsOption("쇼트컷")
        ){
          $warpName = str_replace(['[', ']', '{', '}', ':', '"', '\''], ['', '', '', '', '', '', ''], $warp->getName()); // json syntax
          if(trim($warpName) == ""){
            continue;
          }
          $commands[$warp->getName()] = [
            "versions" => [
              [
                "overloads" => [ "default" => [ "input" => [ "parameters" => [] ] ] ],
                "permission" => "any"
              ]
            ]
          ];
        }
      }
      $event->getPacket()->commands = json_encode($commands);
    }
  }
}
