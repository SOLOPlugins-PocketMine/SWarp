<?php

namespace solo\swarp;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\DataPacketSendEvent;

class ShortcutManager implements Listener{

  private $availableCommandsPacketId = 0x4e;

  public function __construct(SWarp $owner){
    $this->owner = $owner;

    foreach([
      "\\pocketmine\\network\\mcpe\\protocol\\ProtocolInfo",
      "\\pocketmine\\network\\protocol\\ProtocolInfo",
      "\\pocketmine\\network\\protocol\\Info"
    ] as $expectedInterface){
      try{
        if(interface_exists($expectedInterface)){
          $this->availableCommandsPacketId = constant($expectedInterface . "::AVAILABLE_COMMANDS_PACKET");
          $this->owner->getServer()->getLogger()->debug("[SWarp] detected " . $expectedInterface);
        }
      }catch(\Throwable $e){
        continue;
      }
    }

    if($this->owner->getSetting()->get("use-warp-shortcut", true) === true){
      $this->owner->getServer()->getPluginManager()->registerEvents($this, $this->owner);
    }
  }

  public function handleWarpCreate(WarpCreateEvent $event){
    foreach($this->owner->getServer()->getOnlinePlayers() as $player){
      $player->sendCommandData();
    }
  }

  public function handlePlayerCommandPreprocess(PlayerCommandPreprocessEvent $event){
    $text = $event->getMessage();
    if(substr($text, 0, 1) === '/'){
      $find = substr($text, 1);
      if($this->owner->getWarp($find) !== null){
        $event->setMessage('/워프 ' . $find); // via warp command
      }
    }
  }

  public function handleDataPacketSend(DataPacketSendEvent $event){
    if($event->getPacket()->pid() === $this->availableCommandsPacketId){ // AVAILABLE_COMMANDS_PACKET
      $commands = json_decode($event->getPacket()->commands, true);
      foreach($this->owner->getAllWarp() as $warp){
        if(!isset($commands[$warp->getName()])){
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
