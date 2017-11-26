<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\swarp\SWarp;

class WarpPermissionCommand extends Command{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프권한", "워프의 권한을 설정합니다.", "/워프권한 <워프명> <퍼미션>");
    $this->setPermission("swarp.command.setpermission");

    $this->owner = $owner;
  }

  public function execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SWarp::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }

    if(count($args) < 2){
      $sender->sendMessage(SWarp::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SWarp::$prefix . "* swarp.warp.op - 관리자만 사용가능합니다.");
      $sender->sendMessage(SWarp::$prefix . "* swarp.warp.default - 모든 유저가 사용가능합니다.");
      return true;
    }
    $warpName = $args[0];
    $warp = $this->owner->getWarp($warpName);

    if($warp === null){
      $sender->sendMessage(SWarp::$prefix . "\"" . $warpName . "\" 워프는 존재하지 않습니다.");
      return true;
    }
    $perm = $args[1];

    $warp->setPermission($perm);
    $sender->sendMessage(SWarp::$prefix . "\"" . $warp->getName() . "\" 워프의 권한이 \"" . $perm . "\" 으로 설정되었습니다.");
    return true;
  }
}
