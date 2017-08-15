<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\SWarpCommand;
use solo\swarp\Warp;
use solo\swarp\WarpException;

class WarpCommand extends SWarpCommand{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프", "해당 워프 지점으로 이동합니다.", "/워프 <워프명>", ["warp"]);
    $this->setPermission("swarp.command.warp");

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
                //"enum_values" => array_map(
                //  function($warp) use ($player){
                //    return $warp->getName();
                //  },
                //  array_filter(
                //    $this->owner->getAllWarp(),
                //    function($warp) use ($player){
                //      return $player->hasPermission($warp->getPermission());
                //    }
                //  ))
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
      return true;
    }

    $warpName = $args[0];
    $warp = $this->owner->getWarp($warpName);

    if($warp === null){
      $sender->sendMessage(SWarp::$prefix . $warpName . " 워프는 존재하지 않습니다.");
      return true;
    }

    if(!$sender->hasPermission($warp->getPermission())){
      $sender->sendMessage(SWarp::$prefix . "해당 워프로 이동할 권한을 가지고 있지 않습니다.");
      return true;
    }

    try{
      $warp->warp($sender);
    }catch(WarpException $e){
      $sender->sendMessage(SWarp::$prefix . $e->getMessage());
      return true;
    }
    $sender->sendMessage(SWarp::$prefix . $warp->getName() . " 으로 이동하였습니다.");
    return true;
  }

}
