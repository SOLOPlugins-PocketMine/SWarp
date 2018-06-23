<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;
use pocketmine\event\entity\EntityRegainHealthEvent;

class HealOption extends WarpOption{

  /** @var float */
  private $heal;

  public function __construct(ArgumentFloatPositive $heal){
    $this->heal = $heal->getValue();
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

  protected function dataSerialize() : array{
    return [
      "heal" => $this->heal
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->heal = $data["heal"];
  }
}
