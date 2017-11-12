<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\SWarpCommand;

class WarpOptionCommand extends SWarpCommand{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프옵션", "워프의 옵션을 재설정합니다.", "/워프옵션 <워프명> [옵션...]");
    $this->setPermission("swarp.command.setoptions");

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
    if(count($args) < 2){
      $sender->sendMessage(SWarp::$prefix . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SWarp::$prefix . "사용 가능한 옵션 : " . implode(", ", array_keys($this->owner->getWarpOptionFactory()->getAllWarpOptions())));
      return true;
    }

    $warpName = array_shift($args);

    $warp = $this->owner->getWarp($warpName);
    if($warp === null){
      $sender->sendMessage(SWarp::$prefix . "\"" . $warpName . "\" 워프는 존재하지 않습니다.");
      return true;
    }

    try{
      $options = $this->owner->getWarpOptionFactory()->parseOptions(implode(" ", $args));
    }catch(\InvalidArgumentException $e){
      $sender->sendMessage(SWarp::$prefix . $e->getMessage());
      return true;
    }

    $warp->setOptions($options);
    $sender->sendMessage(SWarp::$prefix .$warp->getName() . " 워프의 옵션을 성공적으로 설정하였습니다.");

    foreach($warp->getOptions() as $option){
      $sender->sendMessage(SWarp::$prefix . "* " . $option->__toString());
    }

    $this->owner->save();
    return true;
  }
}
