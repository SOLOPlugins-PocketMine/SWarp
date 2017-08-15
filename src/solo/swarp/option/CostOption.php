<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpEvent;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;

use onebone\economyapi\EconomyAPI;

class CostOption extends WarpOption{

  private $cost;

  public function __construct(string $value = ""){
    if(!is_numeric($value)){
      throw new \InvalidArgumentException("비용은 숫자로 입력해주세요.");
    }
    if($value <= 0){
      throw new \InvalidArgumentException("비용은 음수 또는 0이 될 수 없습니다.");
    }
    $this->cost = $value;
  }

  public function getName() : string{
    return "비용";
  }

  public function test(WarpEvent $event){
    if(EconomyAPI::getInstance()->myMoney($event->getPlayer()) < $this->cost){
      throw new WarpException("워프하는데 비용이 부족합니다. 필요한 비용 : " . $this->cost);
    }
  }

  public function apply(WarpEvent $event){
    EconomyAPI::getInstance()->reduceMoney($event->getPlayer(), $this->cost);
    $event->getPlayer()->sendMessage(SWarp::$prefix . "워프 비용으로 " . $this->cost . "원을 지불하였습니다.");
  }

  public function __toString(){
    return $this->getName() . " : " . $this->cost;
  }

  public function yamlSerialize(){
    $data = parent::yamlSerialize();
    $data["cost"] = $this->cost;
    return $data;
  }

  public static function yamlDeserialize(array $data){
    $option = parent::yamlDeserialize($data);
    $option->cost = $data["cost"];
    return $option;
  }
}
