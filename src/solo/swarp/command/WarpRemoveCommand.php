<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\Warp;
use solo\swarp\event\WarpRemoveEvent;

class WarpRemoveCommand extends Command{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프삭제", "워프를 삭제합니다.", "/워프삭제 <워프명>");
    $this->setPermission("swarp.command.remove");

    $this->owner = $owner;
  }

  public function execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SWarp::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }

    if(empty($args)){
      $sender->sendMessage(SWarp::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $warpName = $args[0];

    $warp = $this->owner->getWarp($warpName);

    if(!$warp instanceof Warp){
      $sender->sendMessage(SWarp::$prefix . "\"" . $warpName . "\" 은(는) 존재하지 않는 워프입니다.");
      return true;
    }
    $ev = new WarpRemoveEvent($warp);
    $this->owner->getServer()->getPluginManager()->callEvent($ev);
    if($ev->isCancelled()){
      $sender->sendMessage(SWarp::$prefix . "워프 제거에 실패하였습니다.");
      return true;
    }
    $this->owner->removeWarp($warp->getName());

    $sender->sendMessage(SWarp::$prefix . "\"" . $warp->getName() . "\" 워프를 제거하였습니다.");

    $this->owner->save(); //save data
    return true;
  }
}
