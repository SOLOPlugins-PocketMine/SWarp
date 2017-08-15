<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\SWarpCommand;
use solo\swarp\Warp;

class WarpCreateCommand extends SWarpCommand{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프생성", "워프를 생성합니다.", "/워프생성 <워프명> [옵션...]");
    $this->setPermission("swarp.command.create");

    $this->owner = $owner;
  }

  public function _generateCustomCommandData(Player $player) : array{
    if(!$player->hasPermission($this->getPermission())){
      return [];
    }
    return [
      "aliases" => $this->getAliases(),
      "overloads" => [
        "default" => [
          "input" => [
            "parameters" => [
              [
                "type" => "rawtext",
                "name" => "워프명",
                "optional" => true
              ],
              [
                "type" => "rawtext",
                "name" => "옵션...",
                "optional" => true
              ]
            ]
          ]
        ]
      ]
    ];
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SWarp::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!$sender instanceof Player){
      $sender->sendMessage(SWarp::$prefix . "인게임에서만 사용할 수 있습니다.");
      return true;
    }

    if(!isset($args[0])){
      $sender->sendMessage(SWarp::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SWarp::$prefix . "사용 가능한 옵션 : " . implode(", ", array_keys($this->owner->getWarpOptionFactory()->getAllWarpOptions())));
      $sender->sendMessage(SWarp::$prefix . "옵션 사용 예시 : /워프생성 테스트 -비용 1000 -쿨타임 3");
      return true;
    }
    $warpName = array_shift($args);

    try{
      $options = $this->owner->getWarpOptionFactory()->parseOptions(implode(" ", $args));
    }catch(\InvalidArgumentException $e){
      $sender->sendMessage(SWarp::$prefix . $e->getMessage());
      return true;
    }

    if($this->owner->getWarp($warpName) instanceof Warp){
      $sender->sendMessage(SWarp::$prefix . "\"" . $warpName . "\" 은(는) 이미 존재하는 워프 이름입니다.");
      return true;
    }
    $warp = new Warp($warpName, $sender->x, $sender->y, $sender->z, $sender->getLevel()->getFolderName(), $options);

    $this->owner->addWarp($warp);

    $sender->sendMessage(SWarp::$prefix . "워프를 생성하였습니다.");

    $sender->sendMessage(SWarp::$prefix . "* 워프 이름 : " . $warp->getName());
    $sender->sendMessage(SWarp::$prefix . "* 월드 : " . $warp->getLevel());
    $sender->sendMessage(SWarp::$prefix . "* 좌표 : x=" . $warp->getX() . ", y=" . $warp->getY() . ", z=" . $warp->getZ());
    foreach($warp->getOptions() as $option){
      $sender->sendMessage(SWarp::$prefix . "* " . $option->__toString());
    }
    return true;
  }
}
