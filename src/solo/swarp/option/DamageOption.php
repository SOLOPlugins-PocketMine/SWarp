<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;
use pocketmine\event\entity\EntityDamageEvent;

class DamageOption extends WarpOption{

  /** @var float */
  private $damage;

  public function __construct(ArgumentFloatPositive $damage){
    $this->damage = $damage->getValue();
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

  protected function dataSerialize() : array{
    return [
      "damage" => $this->damage
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->damage = $data["damage"];
  }
}
