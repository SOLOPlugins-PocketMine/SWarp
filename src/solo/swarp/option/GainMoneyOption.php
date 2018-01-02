<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;
use solo\swarp\option\argument\ArgumentFloatPositive;
use onebone\economyapi\EconomyAPI;

class GainMoneyOption extends WarpOption{

  /** @var float */
  private $amount;

  public function __construct(ArgumentFloatPositive $amount){
    $this->amount = $amount->getValue();
  }

  public function getName() : string{
    return "돈획득";
  }

  public function apply(PlayerWarpEvent $event){
    EconomyAPI::getInstance()->addMoney($event->getPlayer(), $this->amount);
    $event->getPlayer()->sendMessage(SWarp::$prefix . "워프하여 " . $this->amount . "원을 획득하였습니다.");
  }

  public function __toString(){
    return $this->getName() . " : " . $this->cost;
  }

  /*
  public function jsonSerialize() : array{
    $data = parent::jsonSerialize();
    $data["cost"] = $this->cost;
    return $data;
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $option = parent::jsonDeserialize($data);
    $option->cost = $data["cost"];
    return $option;
  }
  */
}
