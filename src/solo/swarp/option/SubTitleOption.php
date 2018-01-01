<?php

namespace solo\swarp\option;

use solo\swarp\SWarp;
use solo\swarp\WarpException;
use solo\swarp\WarpOption;
use solo\swarp\event\PlayerWarpEvent;

class SubTitleOption extends WarpOption{

  private $subTitleMessage;

  public function __construct(string $value = ""){
    $this->subTitleMessage = $value;
  }

  public function getName() : string{
    return "서브타이틀";
  }

  public function apply(PlayerWarpEvent $event){
    SWarp::getInstance()->getTitleManager()->addSubTitle($event->getPlayer(), $this->subTitleMessage);
  }

  public function __toString(){
    return $this->getName() . " : " . $this->subTitleMessage;
  }

  public function jsonSerialize() : array{
    $data = parent::jsonSerialize();
    $data["subTitleMessage"] = $this->subTitleMessage;
    return $data;
  }

  public static function jsonDeserialize(array $data) : WarpOption{
    $option = parent::jsonDeserialize($data);
    $option->subTitleMessage = $data["subTitleMessage"];
    return $option;
  }
}
