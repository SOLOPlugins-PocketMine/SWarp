<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\SWarpCommand;

class WarpPermissionCommand extends SWarpCommand{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프권한", "워프의 권한을 설정합니다.", "/워프권한 <워프명> <퍼미션>");
    $this->setPermission("swarp.command.setpermission");

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
                // MCPE Command Auto Complete does not support Unicode... WTF
                //
                //"enum_values" => array_map(function($warp) use ($player){ return $warp->getName(); }, $this->owner->getAllWarp())
              ],
              [
                "type" => "rawtext",
                "name" => "퍼미션",
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

    if(!isset($args[1])){
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
