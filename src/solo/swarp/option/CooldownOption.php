<?php

namespace solo\swarp\option;

use solo\swarp\WarpEvent;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;

class CooldownOption extends WarpOption{

  private $cooldown;
  private $cooldownList;

  public function __construct(string $value = ""){
    if(!is_numeric($value)){
      throw new \InvalidArgumentException("쿨타임은 숫자이어야합니다.");
    }
    if($value <= 0){
      throw new \InvalidArgumentException("쿨타임은 음수 또는 0이 될 수 없습니다.");
    }
    $this->cooldown = $value;
    $this->cooldownList = [];
  }

  public function getName() : string{
    return "쿨타임";
  }

  public function test(WarpEvent $event){
    $name = strtolower($event->getPlayer()->getName());
    if(isset($this->cooldownList[$name]) && time() - $this->cooldownList[$name] < $this->cooldown){
      throw new WarpException("아직 워프할 수 없습니다. 남은 시간 : " . (intval($this->cooldown) - (time() - $this->cooldownList[$name])) . "초");
    }
  }

  public function apply(WarpEvent $event){
    $this->cooldownList[strtolower($event->getPlayer()->getName())] = time();
  }

  public function __toString(){
    return $this->getName() . " : " . $this->cooldown . "초";
  }

  public function yamlSerialize(){
    $data = parent::yamlSerialize();
    $data["cooldown"] = $this->cooldown;
    $data["cooldownList"] = $this->cooldownList;
    return $data;
  }

  public static function yamlDeserialize(array $data){
    $option = parent::yamlDeserialize($data);
    $option->cooldown = $data["cooldown"];
    $option->cooldownList = $data["cooldownList"];
    foreach($option->cooldownList as $name => $time){
      if(time() - $time > $option->cooldown){
        unset($option->cooldownList[$name]);
      }
    }
    return $option;
  }
}
