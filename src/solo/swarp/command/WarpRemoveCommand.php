<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\swarp\SWarp;
use solo\swarp\Warp;
use solo\swarp\WarpException;

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

    try{
      $this->owner->removeWarp(array_shift($args));
    }catch(WarpException $e){
      $sender->sendMessage(SWarp::$prefix . $e->getMessage());
    }
    $sender->sendMessage(SWarp::$prefix . "\"" . $warp->getName() . "\" 워프를 제거하였습니다.");
    return true;
  }
}
