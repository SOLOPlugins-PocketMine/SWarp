<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\SWarpCommand;

class WarpDescriptionCommand extends SWarpCommand{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프설명", "워프의 설명을 설정합니다.", "/워프설명 <워프명> <설명...>");
    $this->setPermission("swarp.command.setdescription");

    $this->owner = $owner;
  }

  public function _generateCustomCommandData(Player $player) : array{
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
                "name" => "설명...",
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
    if(count($args) < 2){
      $sender->sendMessage(SWarp::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      return true;
    }
    $warpName = array_shift($args);
    $warp = $this->owner->getWarp($warpName);

    if($warp === null){
      $sender->sendMessage(SWarp::$prefix . "\"" . $warpName . "\" 워프는 존재하지 않습니다.");
      return true;
    }
    $description = implode(" ", $args);

    $warp->setDescription($description);
    $sender->sendMessage(SWarp::$prefix . "\"" . $warp->getName() . "\" 워프의 설명이 \"" . $description . "\" 으로 설정되었습니다.");
    return true;
  }
}
