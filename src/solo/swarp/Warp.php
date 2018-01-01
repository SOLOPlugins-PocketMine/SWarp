<?php

namespace solo\swarp;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use solo\swarp\WarpOptionFactory;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\event\WarpOptionUpdateEvent;

class Warp extends Vector3{

  /** @var string */
  protected $name;

  /** @var string */
  protected $level;

  /** @var WarpOption[] */
  protected $options = [];

  /** @var string */
  protected $description = "";

  /** @var string */
  protected $permission = "swarp.warp.default";

  public function __construct(string $name, float $x, float $y, float $z, string $level){
    parent::__construct($x, $y, $z);
    $this->name = strtolower($name);
    $this->level = $level;
  }

  public function getName() : string{
    return $this->name;
  }

  public function getLevel() : string{
    return $this->level;
  }

  public function warp(Player $player, bool $force = false){
    $level = Server::getInstance()->getLevelByName($this->level);
    if($level === null || $level->isClosed()){
      throw new WarpException($this->level . " 은 로드되지 않았거나 존재하지 않는 월드입니다.");
    }
    $event = new PlayerWarpEvent($this, $player, new Position($this->x, $this->y, $this->z, $level));

    foreach($this->options as $option){
      $option->test($event);
    }
    Server::getInstance()->getPluginManager()->callEvent($event);
    if($event->isCancelled() && $false !== true){
      throw new WarpException("워프에 실패하였습니다.");
    }

    foreach($this->options as $option){
      $option->apply($event);
    }
    $player->teleport($event->getDestination());
  }

  public function hasDescription() : bool{
    return !empty($this->description);
  }

  public function getDescription() : string{
    return $this->description;
  }

  public function setDescription(string $description) : Warp{
    $this->description = $description;
    return $this;
  }

  public function getPermission() : string{
    return $this->permission;
  }

  public function setPermission(string $permission) : Warp{
    $this->permission = $permission;
    return $this;
  }

  public function hasOptions() : bool{
    return !empty($this->options);
  }

  public function hasOption(string $name) : bool{
    return isset($this->options[$name]);
  }

  public function getOptions() : array{
    return $this->options;
  }

  public function getOption(stirng $name) : ?WarpOption{
    return $this->options[$name] ?? null;
  }

  public function setOptions(array $options) : Warp{
    $this->options = $options;
    Server::getInstance()->getPluginManager()->callEvent(new WarpOptionUpdateEvent($this));
    return $this;
  }

  public function asPosition() : Position{
    return Position::fromObject($this->asVector3(), Server::getInstance()->getLevelByName($this->level));
  }

  public function __toString(){
    return $this->name
    . " (x=" . $this->x . ", y=" . $this->y . ", z=" . $this->z . ", level=" . $this->level . ", permission=" . $this->permission . ")"
    . (count($this->options) > 0 ? " " . implode(", ", array_map(function($option){ return $option->__toString(); }, $this->options)) : "");
  }

  public function jsonSerialize() : array{
    $optionsData = [];
    foreach($this->options as $option){
      $optionsData[$option->getName()] = $option->jsonSerialize();
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

  public static function jsonDeserialize(array $data) : Warp{
    $warp = (new \ReflectionClass(static::class))->newInstanceWithoutConstructor();
    $warp->name = $data["name"];
    $warp->x = $data["x"];
    $warp->y = $data["y"];
    $warp->z = $data["z"];
    $warp->level = $data["level"];
    $warp->description = $data["description"];
    $warp->permission = $data["permission"];

    $options = [];
    foreach($data["options"] as $optionName => $optionData){
      $optionClass = WarpOptionFactory::getWarpOption($optionName);

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

      $optionInstance = $optionClass::jsonDeserialize($optionData);
      $options[$optionInstance->getName()] = $optionInstance;
    }
    $warp->options = $options;
    return $warp;
  }
}
