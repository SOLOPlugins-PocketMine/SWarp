<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use solo\swarp\SWarp;
use solo\swarp\Warp;
use solo\swarp\WarpOptionFactory;

class WarpCreateCommand extends Command{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프생성", "워프를 생성합니다.", "/워프생성 <워프명> [옵션...]");
    $this->setPermission("swarp.command.create");

    $this->owner = $owner;
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

    if(empty($args)){
      $sender->sendMessage(SWarp::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SWarp::$prefix . "사용 가능한 옵션 : " . implode(", ", array_keys(WarpOptionFactory::getAllWarpOptions())));
      $sender->sendMessage(SWarp::$prefix . "옵션 사용 예시 : /워프생성 테스트 -비용 1000 -쿨타임 3");
      return true;
    }
    $warpName = array_shift($args);

    $warp = new Warp($warpName, $sender->x, $sender->y, $sender->z, $sender->getLevel()->getFolderName());
    try{
      $this->owner->addWarp($warp->setOptions(WarpOptionFactory::parseOptions(implode(" ", $args))));
    }catch(\InvalidArgumentException | WarpException $e){
      $sender->sendMessage(SWarp::$prefix . $e->getMessage());
      return true;
    }

    $sender->sendMessage(SWarp::$prefix . "워프를 생성하였습니다.");
    $sender->sendMessage(SWarp::$prefix . "* 워프 이름 : " . $warp->getName());
    $sender->sendMessage(SWarp::$prefix . "* 월드 : " . $warp->getLevel());
    $sender->sendMessage(SWarp::$prefix . "* 좌표 : x=" . $warp->getX() . ", y=" . $warp->getY() . ", z=" . $warp->getZ());
    foreach($warp->getOptions() as $option){
      $sender->sendMessage(SWarp::$prefix . "* " . $option->__toString());
    }

    $this->owner->save(); //save data
    return true;
  }
}
