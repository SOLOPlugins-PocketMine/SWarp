<?php

namespace solo\swarp;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\math\Vector3;

use solo\swarp\option\ShortcutOption;

class Warp{

  public $name;
  public $x;
  public $y;
  public $z;
  public $level;

  /** @var WarpOption[] */
  public $options;

  public $description = "";

  public $permission = "swarp.warp.default";

  public function __construct(string $name, $x, $y, $z, $level, $options = []){
    $this->name = strtolower($name);
    $this->x = $x;
    $this->y = $y;
    $this->z = $z;
    $this->level = $level;
    $this->options = $options;
  }

  public function getName() : string{
    return $this->name;
  }

  public function getX(){
    return $this->x;
  }

  public function getY(){
    return $this->y;
  }

  public function getZ(){
    return $this->z;
  }

  public function getLevel() : string{
    return $this->level;
  }

  public function warp(Player $player){
    $level = Server::getInstance()->getLevelByName($this->level);
    if($level === null){
      throw new WarpException($this->level . " 은 로드되지 않았거나 존재하지 않는 월드입니다.");
    }
    $event = new WarpEvent($this, $player, new Position($this->x, $this->y, $this->z, $level));

    foreach($this->options as $option){
      $option->test($event);
    }

    foreach($this->options as $option){
      $option->apply($event);
    }
    $player->teleport($event->getDestination());
  }

  public function hasDescription() : bool{
    return $this->description !== "";
  }

  public function setDescription(string $description){
    $this->description = $description;
  }

  public function getDescription() : string{
    return $this->description;
  }

  public function getPermission() : string{
    return $this->permission;
  }

  public function setPermission(string $permission){
    $this->permission = $permission;
  }

  public function addOption(WarpOption $option){
    $this->options[$option->getName()] = $option;
  }

  public function hasOption(){
    return count($this->options) > 0;
  }

  public function getOptions(){
    return $this->options;
  }

  public function setOptions(array $options){
    $this->options = $options;
  }

  public function containsOption(string $optionName){
    return isset($this->options[$optionName]);
  }

  public function removeOption($option){
    if($option instanceof WarpOption){
      $option = $option->getName();
    }
    unset($this->options[$option]);
  }

  public function __toString(){
    return $this->name
    . " (x=" . $this->x . ", y=" . $this->y . ", z=" . $this->z . ", level=" . $this->level . ", permission=" . $this->permission . ")"
    . (count($this->options) > 0 ? " " . implode(", ", array_map(function($option){ return $option->__toString(); }, $this->options)) : "");
  }

  public function yamlSerialize(){
    $optionsData = [];
    foreach($this->options as $option){
      $optionsData[$option->getName()] = $option->yamlSerialize();
    }

    return [
      "name" => $this->name,
      "x" => $this->x,
      "y" => $this->y,
      "z" => $this->z,
      "level" => $this->level,
      "description" => $this->description,
      "permission" => $this->permission,
      "options" => $optionsData
    ];
  }

  public static function yamlDeserialize(array $data){
    $ref = new \ReflectionClass(static::class);
    $warp = $ref->newInstanceWithoutConstructor();
    $warp->name = $data["name"];
    $warp->x = $data["x"];
    $warp->y = $data["y"];
    $warp->z = $data["z"];
    $warp->level = $data["level"];
    $warp->description = $data["description"];
    $warp->permission = $data["permission"];

    $options = [];
    foreach($data["options"] as $optionName => $optionData){
      $optionClass = SWarp::getInstance()->getWarpOptionFactory()->getWarpOption($optionName);

      if($optionClass === null){
        Server::getInstance()->getLogger()->critical("[SWarp] \"" . $optionName . "\" 옵션을 찾을 수 없습니다.");
        continue;
      }else if(!class_exists($optionClass, true)){
        Server::getInstance()->getLogger()->critical("[SWarp] " . $optionClass . " 클래스를 찾을 수 없습니다.");
        continue;
      }else if(!is_subclass_of($optionClass, WarpOption::class)){
        Server::getInstance()->getLogger()->critical("[SWarp] " . $optionClass . " 클래스는 " . WarpOption::class . " 의 서브클래스가 아닙니다.");
        continue;
      }

      $optionInstance = $optionClass::yamlDeserialize($optionData);
      $options[$optionInstance->getName()] = $optionInstance;
    }
    $warp->options = $options;
    return $warp;
  }
}
