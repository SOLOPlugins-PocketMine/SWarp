<?php

namespace solo\swarp\option;

use pocketmine\level\Position;

use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;

class RandomDestinationOption extends WarpOption{

  /** @var float */
  private $range;

  public function __construct(ArgumentFloatPositive $range){
    $this->range = $range->getValue();
  }

  public function getName() : string{
    return "무작위도착지점";
  }

  public function test(PlayerWarpEvent $event){
    $origin = $event->getDestination();
    $event->setDestination(new Position(
      $origin->getX() + (mt_rand(0, 20000) / 10000 * $this->range - $this->range),
      $origin->getY(), // + (mt_rand(0, 10000) / 10000 * $this->range),
      $origin->getZ() + (mt_rand(0, 20000) / 10000 * $this->range - $this->range),
      $origin->getLevel()
    ));
  }

  public function __toString(){
    return $this->getName() . " 범위 : " . $this->range;
  }

  /*
  public function jsonSerialize() : array{
    $data = parent::jsonSerialize();
    $data["range"] = $this->range;
    return $data;
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $option = parent::jsonDeserialize($data);
    $option->range = $data["range"];
    return $option;
  }
  */
}
