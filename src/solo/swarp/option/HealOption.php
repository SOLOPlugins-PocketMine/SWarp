<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;

use pocketmine\event\entity\EntityRegainHealthEvent;

class HealOption extends WarpOption{

  private $heal;

  public function __construct(string $value = ""){
    if(!is_numeric($value)){
      throw new \InvalidArgumentException("회복량은 숫자로 입력해주세요.");
    }
    if($value <= 0){
      throw new \InvalidArgumentException("회복량은 음수 또는 0이 될 수 없습니다.");
    }
    $this->heal = $value;
  }

  public function getName() : string{
    return "회복";
  }

  public function apply(PlayerWarpEvent $event){
    $event->getPlayer()->attack(new EntityRegainHealthEvent($event->getPlayer(), $this->heal, EntityRegainHealthEvent::CAUSE_MAGIC));
    $event->getPlayer()->sendMessage(SWarp::$prefix . "워프하여 체력이 " . $this->heal . " 만큼 회복되었습니다");
  }

  public function __toString(){
    return $this->getName() . " : " . $this->heal;
  }

  public function yamlSerialize(){
    $data = parent::yamlSerialize();
    $data["heal"] = $this->heal;
    return $data;
  }

  public static function yamlDeserialize(array $data){
    $option = parent::yamlDeserialize($data);
    $option->heal = $data["heal"];
    return $option;
  }
}
