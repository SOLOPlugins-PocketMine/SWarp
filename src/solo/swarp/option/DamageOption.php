<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;

use pocketmine\event\entity\EntityDamageEvent;

class DamageOption extends WarpOption{

  private $damage;

  public function __construct(int $value){
    if($value <= 0){
      throw new \InvalidArgumentException("데미지는 음수 또는 0이 될 수 없습니다.");
    }
    $this->damage = $value;
  }
  
  public function __construct(int $value){
    if($value <= 0){
      throw new \InvalidArgumentException("데미지는 음수 또는 0이 될 수 없습니다.");
    }
    $this->damage = $value;
  }

  public function getName() : string{
    return "데미지";
  }

  public function test(PlayerWarpEvent $event){
    if($event->getPlayer()->getHealth() < $this->damage){
      throw new WarpException("워프하는데 체력이 부족합니다. 최소한 " . $this->damage . " 만큼의 체력이 있어야합니다.");
    }
  }

  public function apply(PlayerWarpEvent $event){
    $event->getPlayer()->attack(new EntityDamageEvent($event->getPlayer(), EntityDamageEvent::CAUSE_MAGIC, $this->damage));
    $event->getPlayer()->sendMessage(SWarp::$prefix . "워프하여 체력이 " . $this->damage . " 만큼 소모되었습니다");
  }

  public function __toString(){
    return $this->getName() . " : " . $this->damage;
  }

  public function yamlSerialize(){
    $data = parent::yamlSerialize();
    $data["damage"] = $this->damage;
    return $data;
  }

  public static function yamlDeserialize(array $data){
    $option = parent::yamlDeserialize($data);
    $option->damage = $data["damage"];
    return $option;
  }
}
