<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\swarp\SWarp;
use solo\swarp\Warp;

class ShortcutCommand extends Command{

  /** @var SWarp */
  private $owner;

  /** @var Warp */
  private $warp;

  public function __construct(SWarp $owner, Warp $warp){
    parent::__construct($warp->getName(), $warp->getName() . " (으)로 이동하는 명령어입니다.", "/" . $warp->getName());
    $this->setPermission("swarp.command.warp");

    $this->owner = $owner;
    $this->warp = $warp;
  }

  public function getWarp() : Warp{
    return $this->warp;
  }

  public function execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender instanceof Player){
      $sender->sendMessage(SWarp::$prefix . "인게임에서만 사용할 수 있습니다.");
      return true;
    }
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SWarp::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!$sender->hasPermission($this->warp->getPermission())){
      $sender->sendMessage(SWarp::$prefix . $this->warp->getName() . " (으)로 이동할 권한을 가지고 있지 않습니다.");
      return true;
    }

    try{
      $this->warp->warp($sender);
    }catch(WarpException $e){
      $sender->sendMessage(SWarp::$prefix . $e->getMessage());
      return true;
    }
    $sender->sendMessage(SWarp::$prefix . $this->warp->getName() . " (으)로 이동하였습니다.");
    return true;
  }
}
