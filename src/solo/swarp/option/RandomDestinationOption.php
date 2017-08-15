<?php

namespace solo\swarp\option;

use pocketmine\level\Position;

use solo\swarp\WarpEvent;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;

class RandomDestinationOption extends WarpOption{

  private $range;

  public function __construct(string $value = ""){
    if($value === ""){
      $value = 5;
    }
    if(!is_numeric($value)){
      return new \InvalidArgumentException("범위는 숫자로 적어주세요.");
    }
    if($value <= 0){
      return new \InvalidArgumentException("범위는 음수 또는 0이 될 수 없습니다.");
    }
    $this->range = $value;
  }

  public function getName() : string{
    return "무작위도착지점";
  }

  public function test(WarpEvent $event){
    $origin = $event->getDestination();
    $event->setDestination(new Position(
      $origin->getX() + (mt_rand(0, 20000) / 10000 * $this->range - $this->range),
      $origin->getY(), // + (mt_rand(0, 10000) / 10000 * $this->range),
      $origin->getZ() + (mt_rand(0, 20000) / 10000 * $this->range - $this->range),
      $origin->getLevel()
    ));
  }

  public function apply(WarpEvent $event){

  }

  public function __toString(){
    return $this->getName() . " 범위 : " . $this->range;
  }

  public function yamlSerialize(){
    $data = parent::yamlSerialize();
    $data["range"] = $this->range;
    return $data;
  }

  public static function yamlDeserialize(array $data){
    $option = parent::yamlDeserialize($data);
    $option->range = $data["range"];
    return $option;
  }
}
