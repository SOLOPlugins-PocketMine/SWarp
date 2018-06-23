<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;
use onebone\economyapi\EconomyAPI;

class CostOption extends WarpOption{

  /** @var float */
  private $cost;

  public function __construct(ArgumentFloatPositive $amount){
    $this->cost = $amount->getValue();
  }

  public function getName() : string{
    return "비용";
  }

  public function test(PlayerWarpEvent $event){
    if(EconomyAPI::getInstance()->myMoney($event->getPlayer()) < $this->cost){
      throw new WarpException("워프하는데 비용이 부족합니다. 필요한 비용 : " . $this->cost);
    }
  }

  public function apply(PlayerWarpEvent $event){
    EconomyAPI::getInstance()->reduceMoney($event->getPlayer(), $this->cost);
    $event->getPlayer()->sendMessage(SWarp::$prefix . "워프 비용으로 " . $this->cost . "원을 지불하였습니다.");
  }

  public function __toString(){
    return $this->getName() . " : " . $this->cost;
  }

  protected function dataSerialize() : array{
    return [
      "cost" => $this->cost
    ];
  }

  protected function dataDeserialize(array $data) : void{
    $this->cost = $data["cost"];
  }
}
