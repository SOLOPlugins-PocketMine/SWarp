<?php

namespace solo\swarp\command;

use pocketmine\Player;
use pocketmine\command\CommandSender;

use solo\swarp\SWarp;
use solo\swarp\SWarpCommand;

class WarpListCommand extends SWarpCommand{

  private $owner;

  public function __construct(SWarp $owner){
    parent::__construct("워프목록", "워프의 목록을 확인합니다.", "/워프목록");
    $this->setPermission("swarp.command.list");

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
              //"type" => "rawtext",
              //"name" => "페이지",
              //"optional" => true
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

    $warps = $this->owner->getAllWarp();

    $maxPage = ceil(count($warps) / 5);
    $page = 1;
    if(isset($args[0]) && is_numeric($args[0])){
      $page = max(1, min($maxPage, $args[0]));
    }

    $sender->sendMessage("§l==========[ 워프 목록 (전체 " . $maxPage . "페이지 중 " . $page . "페이지" . " ]==========");

    $i = 0;
    foreach($warps as $warp){
      if($i < $page * 5 - 5){
        continue;
      }
      if($i >= $page * 5){
        break;
      }
      $i++;
      $message = "§7[" . $i . "] " . ($sender->hasPermission($warp->getPermission()) ? "§a" : "§c") . $warp->getName() . "§7";
      if($warp->hasDescription()){
        $message .= "   " . $warp->getDescription();
      }else{
        if($warp->hasOption()) $message .= "   " . implode(", ", array_map(function($option){ return $option->__toString() . "§7"; }, $warp->getOptions()));
        if($sender->isOp()) $message .= "   (x=" . $warp->getX() . ", y=" . $warp->getY() . ", z=" . $warp->getZ() . ", level=" . $warp->getLevel() . ", permission=" . $warp->getPermission() . ")";
      }
      $sender->sendMessage($message);
    }

    $warps = $this->owner->getAllWarp();
    //$sender->sendMessage(implode(", ", array_map(function($warp) use ($sender){ return ($sender->hasPermission($warp->getPermission()) ? "" : "§c") . $warp->getName() . "§f"; }, $warps)));
    //$sender->sendMessage(SWarp::$prefix . "총 " . count($warps) . "개의 워프가 존재합니다.");
    return true;
  }
}
