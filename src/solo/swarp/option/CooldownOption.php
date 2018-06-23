<?php

namespace solo\swarp\option;

use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;

class CooldownOption extends WarpOption{

  /** @var float */
  private $cooldown;

  /** @var array */
  private $cooldownList;

  public function __construct(ArgumentFloatPositive $seconds){
    $this->cooldown = $seconds->getValue();
    $this->cooldownList = [];
  }

  public function getName() : string{
    return "쿨타임";
  }

  public function test(PlayerWarpEvent $event){
    $name = strtolower($event->getPlayer()->getName());
    if(isset($this->cooldownList[$name]) && time() - $this->cooldownList[$name] < $this->cooldown){
      throw new WarpException("아직 워프할 수 없습니다. 남은 시간 : " . (intval($this->cooldown) - (time() - $this->cooldownList[$name])) . "초");
    }
  }

  public function apply(PlayerWarpEvent $event){
    $this->cooldownList[strtolower($event->getPlayer()->getName())] = time();
  }

  public function __toString(){
    return $this->getName() . " : " . $this->cooldown . "초";
  }

  private function cleanupCooldownList(){
    foreach($this->cooldownList as $name => $time){
      if(time() - $time > $this->cooldown){
        unset($this->cooldownList[$name]);
      }
    }
  }

  protected function dataSerialize() : array{
    $this->cleanupCooldownList();
    return [
      "cooldown" => $this->cooldown,
      "cooldownList" => $this->cooldownList
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->cooldown = $data["cooldown"];
    $this->cooldownList = $data["cooldownList"];
  }
}
